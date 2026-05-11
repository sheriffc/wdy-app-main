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
        Schema::create('cache_attendance_by_school_date', function (Blueprint $table) {
            $table->string('uuid', 52)->primary();
            $table->date('date');
            $table->integer('academic_year');
            $table->string('school_uuid', 40);
            $table->string('school_name');
            $table->float('lat', 10, 0)->nullable();
            $table->float('lng', 10, 0)->nullable();
            $table->string('district_id')->nullable();
            $table->string('chiefdom_id')->nullable();
            $table->string('tablet_phone_number')->nullable();
            $table->string('school_leader_phone_number')->nullable();
            $table->integer('teachers_total')->nullable();
            $table->integer('learners_total')->nullable();
            $table->integer('count_classrooms')->nullable();
            $table->integer('teachers_reported')->nullable();
            $table->integer('teachers_present')->nullable();
            $table->integer('teachers_late')->nullable();
            $table->integer('teachers_absent')->nullable();
            $table->integer('learners_reported')->nullable();
            $table->integer('learners_am_present')->nullable();
            $table->integer('learners_am_absent')->nullable();
            $table->integer('learners_pm_present')->nullable();
            $table->integer('learners_pm_absent')->nullable();
            $table->integer('maternal_am_present_mother')->nullable();
            $table->integer('maternal_am_present_pregnant')->nullable();
            $table->integer('maternal_am_present_pregnant_mother')->nullable();
            $table->integer('maternal_am_absent_mother')->nullable();
            $table->integer('maternal_am_absent_pregnant')->nullable();
            $table->integer('maternal_am_absent_pregnant_mother')->nullable();
            $table->integer('maternal_pm_present_mother')->nullable();
            $table->integer('maternal_pm_present_pregnant')->nullable();
            $table->integer('maternal_pm_present_pregnant_mother')->nullable();
            $table->integer('maternal_pm_absent_mother')->nullable();
            $table->integer('maternal_pm_absent_pregnant')->nullable();
            $table->integer('maternal_pm_absent_pregnant_mother')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cache_attendance_by_school_date');
    }
};
