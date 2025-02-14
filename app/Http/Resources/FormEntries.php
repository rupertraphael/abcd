<?php

namespace App\Http\Resources;

use App\Form;
use App\FormTargetType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\ResourceCollection;

class FormEntries extends ResourceCollection
{
    public $collects = 'App\Http\Resources\FormEntry';

    protected $form;

    protected $targetType;

    protected $fields;

    public function __construct(
        $resource,
        Form $form,
        FormTargetType $targetType,
        Collection $fields)
    {
        parent::__construct($resource);
        $this->form = $form;
        $this->targetType = $targetType->name;
        $this->fields = $fields;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->addFormFieldReferences($this->collection),
            'form_fields' => $this->formFields()
        ];
    }

    public function with($request)
    {
        return [
            'target_type' => $this->targetType,
            'fields' => $this->fields()
        ];
    }

    public function formFields()
    {
        $fields = [];

        $keyed = $this->fields->mapWithKeys(function ($field, $key) {
          return [
            $field['column_name'] => [
              'name' => $field['title'],
              'slug' => $field['column_name'],
              'key' => $field['column_name'],
              'type' => $field['type'],
              'target_type' => $field['target_type'],
              'target' => $field['target']
            ]
          ];
        });

        return $keyed->all();
    }

    public function fields()
    {
        $fields = [
            // commented out so that front end doesn't
            // include this
            // 'target' => [
            //   'slug' => 'target',
            //   'name' => 'About',
            //   'key' => 'target'
            // ],
            'parent_entity' => [
              'name' => 'Attached to...',
              'key' => 'parent_entity'
            ]
          ];

        return $fields;
    }

    private function addFormFieldReferences($collection) {
        foreach($collection as $item) {
            $item->setFormFields($this->formFields());
        }

        return $collection;
    }
}
