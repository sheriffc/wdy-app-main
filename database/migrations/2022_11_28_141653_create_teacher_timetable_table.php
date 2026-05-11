<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacherTimetableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('teacher_timetable')) {
            Schema::create('teacher_timetable', function (Blueprint $table) {
                $table->string('uuid', 40)->primary();
                $table->string('teacher_uuid', 40)->nullable();
                $table->string('school_uuid', 40)->nullable();
                $table->string('school_group_uuid', 40)->nullable();
                $table->string('subject_oid', 100)->nullable();
                $table->string('subject_other')->nullable();
                $table->string('day_name', 30)->nullable();
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();
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
        Schema::dropIfExists('teacher_timetable');
    }
}
