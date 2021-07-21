<?php

use Silber\Bouncer\Database\Models;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBouncerTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
        Schema::connection('redfield')->create(Models::table('assigned_roles'), function (Blueprint $table) {
            //$table->integer('id')->primary()->autoIncrement();
            $table->integer('id')->primary()->type('IDENTITY()');
            $table->string('role_id')->unsigned();
            $table->string('entity_id')->unsigned();
            $table->string('entity_type');
            $table->string('restricted_to_id')->unsigned()->nullable();
            $table->string('restricted_to_type')->nullable();
            $table->integer('scope')->nullable();
        });
        */

        Schema::connection('redfield')->create(Models::table('abilities'), function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('entity_id')->unsigned()->nullable();
            $table->string('entity_type')->nullable();
            $table->boolean('only_owned')->default(false);
            $table->longText('options')->nullable();
            $table->integer('scope')->nullable();
            $table->string('created_at')->nullable();
            $table->string('updated_at')->nullable();
        });

        Schema::connection('redfield')->create(Models::table('roles'), function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('title')->nullable();
            $table->integer('level')->unsigned()->nullable();
            $table->integer('scope')->nullable();
            $table->string('created_at')->nullable();
            $table->string('updated_at')->nullable();
        });

        Schema::connection('redfield')->create('permissions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('ability_id')->unsigned();
            $table->string('entity_id')->unsigned()->nullable();
            $table->string('entity_type')->nullable();
            $table->boolean('forbidden')->default(false);
            $table->integer('scope')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('redfield')->drop('permissions');
        Schema::connection('redfield')->drop(Models::table('assigned_roles'));
        Schema::connection('redfield')->drop(Models::table('roles'));
        Schema::connection('redfield')->drop(Models::table('abilities'));
    }
}
