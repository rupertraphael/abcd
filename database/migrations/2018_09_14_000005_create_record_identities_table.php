<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecordIdentitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('record_identities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 32);
            $table->text('description')->nullable();

            $table->integer('field_1_id')->unsigned();

            $table->integer('field_2_id')->unsigned()->nullable();

            $table->integer('field_3_id')->unsigned()->nullable();

            $table->timestamps();

            $table->foreign('field_1_id')->references('id')->on('record_fields');
            $table->foreign('field_2_id')->references('id')->on('record_fields');
            $table->foreign('field_3_id')->references('id')->on('record_fields');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('record_identities', function (Blueprint $table) {
            $table->dropForeign(['field_1_id']);
            $table->dropForeign(['field_2_id']);
            $table->dropForeign(['field_3_id']);
        });

        Schema::dropIfExists('record_identities');
    }
}
