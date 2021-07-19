<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Silber\Bouncer\Database\Models;

class MakeRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('redfield')->create(Models::table('role'), function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('title')->nullable();
            $table->integer('level')->unsigned()->nullable();
            $table->integer('scope')->nullable();
            $table->string('created_at')->nullable();
            $table->string('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('redfield')->dropIfExists('role');
    }
}
