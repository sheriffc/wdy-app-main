<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('school_group')) {
            Schema::create('school_group', function (Blueprint $table) {
                $table->string('uuid', 40)->primary();
                $table->string('school_uuid', 40)->nullable();
                $table->integer('academic_year')->nullable();
                $table->string('teacher_uuid', 40)->nullable();
                $table->string('school_group_name', 255)->nullable();
                $table->string('school_group_level_oid', 100)->nullable();
                $table->boolean('active')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->integer('created_by');
                $table->timestamp('updated_at')->useCurrentOnUpdate();
                $table->integer('updated_by');
                $table->timestamp('deleted_at')->nullable();
                $table->integer('deleted_by')->nullable();
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
        Schema::dropIfExists('school_group');
    }
}
