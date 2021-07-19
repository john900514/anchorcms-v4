<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSnapshotsTable extends Migration
{
    public function up()
    {
        Schema::connection('event-sourcing')->create('snapshots', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('aggregate_uuid');
            $table->unsignedInteger('aggregate_version');
            $table->longText('state');

            $table->timestamps();

            $table->index('aggregate_uuid');
        });
    }

    public function down()
    {
        Schema::connection('event-sourcing')->dropIfExists('snapshots');
    }
}
