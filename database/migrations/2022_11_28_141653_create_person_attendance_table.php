<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('person_attendance')) {
            Schema::create('person_attendance', function (Blueprint $table) {
                $table->string('uuid', 40)->primary();
                $table->date('date')->nullable();
                $table->string('person_uuid', 40)->nullable();
                $table->string('entity_type_oid', 100)->nullable();
                $table->integer('academic_year')->nullable();
                $table->string('school_uuid', 40)->nullable();
                $table->string('school_group_uuid', 40)->nullable();
                $table->string('attendance_am_status_oid', 100)->nullable();
                $table->string('attendance_pm_status_oid', 100)->nullable();
                $table->string('attendance_status_oid', 100)->nullable();
                $table->string('absent_reason_oid', 100)->nullable();
                $table->text('absent_reason_other')->nullable();
                $table->float('lat', 10, 0)->nullable();
                $table->float('lng', 10, 0)->nullable();
                $table->string('biometric_method_oid', 100)->nullable();
                $table->string('biometric_reference', 40)->nullable();
                $table->tinyInteger('submitted')->nullable();
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
        Schema::dropIfExists('person_attendance');
    }
}
