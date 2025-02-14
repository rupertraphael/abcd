<?php

namespace App;

use App\Entity;
use App\Collection;
use App\Scope;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\SchemalessAttributes\SchemalessAttributes;
use Wildside\Userstamps\Userstamps;


class Form extends Entity
{
    use Userstamps;

    protected $searchColumns = [
        'name'
    ];

    public $casts = [
        'field_layout' => 'array',
    ];

    protected $fieldNumber = 1;

    /**
     * TODO: [Maintenance] make this function easier to read; extract some parts
     * to private functions.
     */
    public function createUsingRequest($request)
    {
        $this->name = $request->name;
        $this->description = $request->description;
        $this->type = $request->type;
        $this->setTableName(DB::table($this->getTable())->max('id')+1);
        $this->target_type_id = $request->target_type_id;
        $this->target_id = $request->target_id;
        $this->scope_id = $request->scope_id;
        $this->field_layout = $request->fields;
        $this->active = TRUE;

        DB::transaction(function () use ($request) {
            $this->save();

            // assign owner to form depending on scope
            if(!empty($request->owner_id)) {

                $scope = Scope::find($request->scope_id);
                $model = new $scope->model_type;
                $collectionType = (new $model)->getCollectionType();
                $this->addParentEntity($collectionType, $request->owner_id);
            }

            if(empty($request->validated()['fields']))
                return;

            $fields = collect($request->validated()['fields']);
            $toBeRemoved = [];
            $toBeFlattened = [];

            $fields = $fields->reject(function($field, $key) {
                return $field['type'] == 'section_divider';
            });

            $fields->transform(function($field, $key) use (&$toBeRemoved, &$toBeFlattened) {
                if($field['type'] != 'MatrixField'){
                    $columnName = $this->generateFieldColumnName($this->fieldNumber++);

                    $field['column_name'] = $columnName;
                    $this->field_layout->set("$key.column_name", $columnName);
                    return $field;
                }

                $radioFields = [];
                $radioFields = collect($field['questions'])
                    ->map(function($question, $radioFieldKey) use ($field, $key){
                        $radioField = [];
                        $radioField['title'] = $question;
                        $radioField['type'] = 'RadioField';
                        $radioField['choices'] = $field['choices'];
                        $radioField['settings'] = $field['settings'];
                        $radioField['validation_rules']
                            = isset($field['validation_rules']) ? $field['validation_rules'] : NULL;
                        $columnName = $this->generateFieldColumnName($this->fieldNumber++);
                        $radioField['column_name'] = $columnName;
                        $this->field_layout->set("$key.questions.$radioFieldKey.column_name", $columnName);
                        $this->field_layout->set("$key.questions.$radioFieldKey.title", $question);
                        return $radioField;
                    });

                array_push($toBeFlattened, $key);

                return $radioFields;
            });

            $added = 0;
            foreach($toBeFlattened as $key)
            {
                $adjustedKey = $key + $added;

                $added = sizeOf($fields[$adjustedKey]) - 1;
                $fields->splice($adjustedKey, 1, $fields[$adjustedKey]->toArray());
            }

            $this->save();

            $this->fields()->createMany($fields->toArray());

            $fields = $this->fields;

            // create form entry table for form
            Schema::create($this->table_name, function (Blueprint $table) use ($fields) {
                $table->bigIncrements('id');

                // TODO: make form types a constant property?
                if($this->type == config('app.form_types.pre-post'))
                    $table->string('type');

                $table->bigInteger('target_id')->unsigned();

                foreach($fields as $field) {
                    $columnType = $field->column_type;
                    $columnName = $field->column_name;

                    $table->$columnType($columnName)->nullable();

                    // foreign keys
                    // if(!empty($field->target_type)) {

                    //     if($field->target_type->name != config('app.form_target_types.form_field.name')) {
                    //         $class = $field->target_type->model;
                    //         $model = new $class;

                    //         $table
                    //             ->foreign($columnName)
                    //             ->references($model->getFormReferenceField())
                    //             ->on($model->getFormReferenceTable());
                    //     }
                    // }
                }

                $table->bigInteger('parent_entity_id')->unsigned();
                $table->bigInteger('parent_entity_type_id')->unsigned();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->userstamps();

                $class = $this->target_type->model;
                $model = new $class;

                $table
                    ->foreign('target_id')
                    ->references($model->getFormReferenceField())
                    ->on($model->getFormReferenceTable());

                $table->foreign('parent_entity_type_id')->references('id')->on('collections');
            });
        });
    }

    public function addParentEntity(Collection $entityType, $entityId) {
        $relationship = $this->belongsToMany('App\Form', 'model_has_forms', 'form_id', 'form_id')->withTimestamps();

        $count = $relationship->where('id', $this->id)
        ->where('model_type', $entityType->model_type)
        ->where('model_id', $entityId)->count();

        if($count > 0)
            return;

        $relationship->attach($this->id, [
            'model_type' => $entityType->model_type,
            'model_id' => $entityId,
            'required' => false
        ]);
    }

    protected function generateFieldColumnName($fieldNumber)
    {
        return 'field_' . $fieldNumber;
    }

    public function setFieldLayoutAttribute($value)
    {
        $this->attributes['field_layout'] = json_encode($value);
    }

    public function getFieldLayoutAttribute(): SchemalessAttributes
    {
        return SchemalessAttributes::createForModel($this, 'field_layout');
    }

    /**
     * TODO: Implement this relationship; might need a custom relationship
     * Form has many entries but is dictated by the entries' table name since
     * a form's entries are located in a table in the form: `form_[id]`
     * where [id] is the left padded id of the form
     */
    public function entries()
    {

    }

    public function fields()
    {
        return $this->hasMany('App\FormField');
    }

    public function target_type()
    {
    	 return $this->belongsTo('App\FormTargetType');
    }

    public function target()
    {
    	 return $this->belongsTo($this->target_type->model);
    }

    public function teams()
    {
        return
            $this
            ->morphedByMany(
                'App\Team',
                'model',
                'model_has_forms',
                'form_id',
                'model_id'
            )
            ->withTimestamps()
            ->withPivot('required');
    }

    /**
     * Associate the current loaded form to the given teams.
     * @param array $id team id(s)
     * @param boolean $required determines if the form be should filled up
     */
    public function addToTeams($ids, $required = false)
    {
        // associate with teams
        $this->teams()->syncWithoutDetaching(
          array_fill_keys($ids, ['required' => $required])
        );
    }

    public function programs()
    {
        return
            $this
            ->morphedByMany(
                'App\Program',
                'model',
                'model_has_forms',
                'form_id',
                'model_id'
            )
            ->withTimestamps()
            ->withPivot('required');
    }

    /**
     * Associate the current loaded form to the given programs.
     * @param array $ids program id(s)
     * @param boolean $required determines if the form be should filled up
     */
    public function addToPrograms($ids, $required = false)
    {
        // associate to programs
        $this->programs()->syncWithoutDetaching(
          array_fill_keys($ids, ['required' => $required])
        );
    }

    public function groups()
    {
        return
            $this
            ->morphedByMany(
                'App\Group',
                'model',
                'model_has_forms',
                'form_id',
                'model_id'
            )
            ->withTimestamps()
            ->withPivot('required');
    }

    /**
     * Associate the current loaded form to the given groups.
     * @param array $ids group id(s)
     * @param boolean $required determines if the form be should filled up
     */
    public function addToGroups($ids, $required = false)
    {
        // associate with groups
        $this->groups()->syncWithoutDetaching(
          array_fill_keys($ids, ['required' => $required])
        );
    }

    public function records()
    {
        return
            $this
            ->morphedByMany(
                'App\Record',
                'model',
                'model_has_forms',
                'form_id',
                'model_id'
            )
            ->withTimestamps()
            ->withPivot('required');
    }


    protected function setTableName($id)
    {
        $this->table_name = 'form_' . str_pad($id, 3, '0', STR_PAD_LEFT);
    }

    public function scope() {
        return $this->belongsTo('App\Scope');
    }

    public function scopeAvailableFor($query, $user)
    {
        $universal =
            (clone $query)
                ->where('scope_id', Scope::where('name', config('auth.scopes.universal.name'))->first()->id);
        $teams = (clone $query)->inTeams($user->teams);
        $programs = (clone $query)->inPrograms($user->programs);
        $groups = (clone $query)->inGroups($user->groups);
        $self = (clone $query)->inSelf($user);

        $query = $universal->union($teams)
                ->union($programs)
                ->union($groups)
                ->union($self);

        return $query;
    }

    public function scopeInTeams($query, $teams)
    {
        return
            $query
                ->where('scope_id', Scope::where('name', config('auth.scopes.team.name'))->first()->id)
                ->whereHas('teams', function ($query) use ($teams) {
                    return $query->whereIn('model_id', $teams->pluck('id'));
                });
    }

    public function scopeInPrograms($query, $programs)
    {
        return
            $query
                ->where('scope_id', Scope::where('name', config('auth.scopes.program.name'))->first()->id)
                ->whereHas('programs', function ($query) use ($programs) {
                    return $query->whereIn('model_id', $programs->pluck('id'));
                });
    }

    public function scopeInGroups($query, $groups)
    {
        return
            $query
                ->where('scope_id', Scope::where('name', config('auth.scopes.group.name'))->first()->id)
                ->whereHas('groups', function ($query) use ($groups) {
                    return $query->whereIn('model_id', $groups->pluck('id'));
                });
    }

    public function scopeInSelf($query, $user)
    {
        return
            $query
                ->where('created_by', $user->id);
    }

    public function scopeSort($query, $column, $ascending)
    {
        if(empty($column))
            return ;

        if($ascending == 'true')
            $ascending = 'asc';
        else
            $ascending = 'desc';

        return $query->orderBy($column, $ascending);
    }

    public function scopeSearch($query, $term)
    {
        if(empty($term))
            return $query;

        return $query->where('name', 'LIKE' , '%' . $term . '%')
        ->orWhere('description', 'LIKE', '%' . $term . '%');
    }

    public function getPathAttribute()
    {
        return "/$this->table/$this->id";
    }
}
