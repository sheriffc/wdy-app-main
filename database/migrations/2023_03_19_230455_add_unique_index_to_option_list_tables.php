<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('option_list')) {
            Schema::table('option_list', function (Blueprint $table) {
                $table->unique(['list_name', 'item_id'], 'ol_unique');
            });
        }

        if(Schema::hasTable('option_list_link')) {
            Schema::table('option_list_link', function (Blueprint $table) {
                $table->unique(['parent_list_name', 'child_list_name', 'parent_id', 'child_id'], 'oll_unique');
                $table->index('updated_at', 'sync');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(Schema::hasTable('option_list')) {
            Schema::table('option_list', function (Blueprint $table) {
                $table->dropIndex('ol_unique');
            });
        }

        if(Schema::hasTable('option_list_link')) {
            Schema::table('option_list_link', function (Blueprint $table) {
                $table->dropIndex('oll_unique');
                $table->dropIndex('sync');
            });
        }
    }
};
