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
        if (!Schema::hasTable('user_scope_group_user_link')) {
            Schema::create('user_scope_group_user_link', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('group_id');
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('user_scope_group')) {
            Schema::create('user_scope_group', function (Blueprint $table) {
                $table->id();
                $table->string('group_name', 100);
                $table->text('group_description')->nullable();
                $table->text('district_selection')->nullable();
                $table->text('school_selection')->nullable();
                $table->integer('display_order')->nullable();
                $table->tinyInteger('active')->default(1);
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrentOnUpdate();
            });
        }

        if (!Schema::hasTable('user_scope_custom_assignment')) {
            Schema::create('user_scope_custom_assignment', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->string('school_uuid', 50);
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('user_scope_cache')) {
            Schema::create('user_scope_cache', function (Blueprint $table) {
                $table->id();
                $table->string('hash', 50);
                $table->text('school_uuids')->nullable();
                $table->timestamp('created_at')->useCurrent();
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
        Schema::dropIfExists('user_scope_group_user_link');
        Schema::dropIfExists('user_scope_group');
        Schema::dropIfExists('user_scope_custom_assignment');
        Schema::dropIfExists('user_scope_cache');
    }
};
