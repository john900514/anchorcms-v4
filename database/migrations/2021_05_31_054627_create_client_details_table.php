<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('redfield')->create('client_details', function (Blueprint $table) {
            $table->string('id')->primary()->unique();

            $table->string('client_id');
            $table->string('detail');
            $table->string('value',65535)->nullable();
            $table->string('misc', 65535)->nullable();

            $table->boolean('active')->default(1);
            $table->string('created_at')->nullable();
            $table->string('updated_at')->nullable();
            $table->string('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('redfield')->dropIfExists('client_details');
    }
}
