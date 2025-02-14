<?php

namespace App;

use App\Model;

class FormTargetType extends Model
{
   	protected static function boot()
    {
    	parent::boot();

    	static::addGlobalScope(function ($query) {
    		$query
    			->where('name', '!=', config('app.form_target_types.form_field.name'))
    			->where('name', '!=', config('app.form_target_types.form.name'));
    	});
    }
}
