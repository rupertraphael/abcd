<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 32);
            $table->text('description')->nullable();
            $table->integer('team_id')->unsigned();
            $table->integer('default_client_status_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('team_id')->references('id')->on('teams');
            $table->foreign('default_client_status_id')->references('id')->on('client_statuses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
        });

        Schema::dropIfExists('programs');
    }
}