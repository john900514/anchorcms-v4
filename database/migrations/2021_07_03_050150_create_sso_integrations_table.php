<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSsoIntegrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sso_integrations', function (Blueprint $table) {
            $table->string('id')->primary()->unique();
            $table->string('name');
            $table->string('client_id')->nullable();
            $table->string('service_id')->nullable();
            $table->string('slug');
            $table->string('base_url');
            $table->string('default_route')->default('/');
            $table->longText('public_key')->nullable();
            $table->longText('private_key')->nullable();
            $table->text('permitted_role')->nullable();
            $table->text('permitted_ability')->nullable();

            $table->boolean('active')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sso_integrations');
    }
}
