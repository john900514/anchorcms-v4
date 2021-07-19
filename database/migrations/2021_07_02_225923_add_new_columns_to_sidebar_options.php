<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsToSidebarOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sidebar_options', function (Blueprint $table) {
            $table->boolean('is_standalone')->default(0)->after('is_submenu');
            $table->boolean('is_modal')->default(0)->after('is_standalone');
            $table->boolean('is_post_action')->default(0)->after('is_modal');
            $table->string('action_url')->nullable()->after('is_post_action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sidebar_options', function (Blueprint $table) {
            $table->dropColumn('is_standalone');
            $table->dropColumn('is_modal');
            $table->dropColumn('is_post_action');
            $table->dropColumn('action_url');
        });
    }
}
