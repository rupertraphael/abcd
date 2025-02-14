<?php

use Faker\Generator as Faker;

use App\RecordField;
use App\Record;
use App\RecordType;

$factory->define(Record::class, function (Faker $faker) {
    $recordType = RecordType::inRandomOrder()->first();

    $fields = array();
    $values = array('', '', '');
    $fields[0] = RecordField::find($recordType->identity->field_1_id);
    $fields[1] = RecordField::find($recordType->identity->field_2_id) ?? null;
    $fields[2] = RecordField::find($recordType->identity->field_3_id) ?? null;

    foreach($fields as $index=>$field) {
        if($field == null) {
            continue;
        }

        switch($field->slug) {
            case 'first_name':
                $values[$index] = $faker->firstName();
                break;

            case 'last_name':
                $values[$index] = $faker->lastName();
                break;

            case 'business_name':
                $values[$index] = $faker->company;
                break;

            case 'email_address':
                $values[$index] = $faker->freeEmail;
                break;

            case 'birth_date':
                $values[$index] = $faker->date;
                break;

            case 'contact_number':
                $values[$index] = $faker->phoneNumber;
                break;

            case 'name':
                $values[$index] = $faker->firstName();
                break;
        }
    }

    return [
        'record_type_id' => $recordType->id,
        'field_1_value' => $values[0],
        'field_2_value' => $values[1],
        'field_3_value' => $values[2],
        'active' => true
    ];
});
