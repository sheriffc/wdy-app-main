<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('option_list')) {
            Schema::create('option_list', function (Blueprint $table) {
                $table->integer('id')->primary();
                $table->integer('parent_id')->nullable();
                $table->string('list_name', 100)->nullable();
                $table->string('item_name')->nullable();
                $table->string('item_id', 100)->nullable();
                $table->string('item_extra')->nullable();
                $table->string('item_asc_fabinc_recordid')->nullable();
                $table->integer('display_order')->nullable();
                $table->tinyInteger('active')->default(1);
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrentOnUpdate();
                $table->timestamp('deleted_at')->nullable();
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
        Schema::dropIfExists('option_list');
    }
}
