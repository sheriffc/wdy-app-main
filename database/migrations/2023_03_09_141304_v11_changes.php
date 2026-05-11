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
        if (Schema::hasTable('school')) {
            Schema::table('school', function (Blueprint $table) {
                $table->renameColumn('education_level_oid', 'school_education_level_oid');
            });
        }
        if (Schema::hasTable('school_learner_admission')) {
            Schema::table('school_learner_admission', function (Blueprint $table) {
                $table->renameColumn('end_reason_oid', 'end_reason_learner_oid');
                $table->renameColumn('end_reason_other', 'end_reason_learner_other');
                $table->renameColumn('end_reason_detail', 'end_reason_learner_detail');
            });
        }
        if (Schema::hasTable('teacher')) {
            Schema::table('teacher', function (Blueprint $table) {
                $table->renameColumn('employment_role_oid', 'teacher_role_oid');
                $table->renameColumn('end_reason_oid', 'end_reason_teacher_oid');
                $table->renameColumn('end_reason_other', 'end_reason_teacher_other');
                $table->renameColumn('end_reason_detail', 'end_reason_teacher_detail');
            });
        }
        if (Schema::hasTable('teacher_timetable')) {
            Schema::table('teacher_timetable', function (Blueprint $table) {
                $table->renameColumn('subject_oid', 'school_subject_oid');
                $table->renameColumn('subject_other', 'school_subject_other');
            });
        }
        if (Schema::hasTable('learner')) {
            Schema::table('learner', function (Blueprint $table) {
                $table->renameColumn('strongest_language_oid', 'language_oid_strongest');
                $table->renameColumn('disability_vision_oid', 'disability_severity_oid_vision');
                $table->renameColumn('disability_hearing_oid', 'disability_severity_oid_hearing');
                $table->renameColumn('disability_mobility_oid', 'disability_severity_oid_mobility');
                $table->renameColumn('disability_cognition_oid', 'disability_severity_oid_cognition');
                $table->renameColumn('disability_selfcare_oid', 'disability_severity_oid_selfcare');
                $table->renameColumn('disability_communication_oid', 'disability_severity_oid_communication');
            });
        }
        if (Schema::hasTable('media_photo')) {
            Schema::table('media_photo', function (Blueprint $table) {
                $table->renameColumn('binary_data', 'base64_data');
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
        if (Schema::hasTable('school')) {
            Schema::table('school', function (Blueprint $table) {
                $table->renameColumn('school_education_level_oid', 'education_level_oid');
            });
        }
        if (Schema::hasTable('school_learner_admission')) {
            Schema::table('school_learner_admission', function (Blueprint $table) {
                $table->renameColumn('end_reason_learner_oid', 'end_reason_oid');
                $table->renameColumn('end_reason_learner_other', 'end_reason_other', );
                $table->renameColumn('end_reason_learner_detail', 'end_reason_detail');
            });
        }
        if (Schema::hasTable('teacher')) {
            Schema::table('teacher', function (Blueprint $table) {
                $table->renameColumn('teacher_role_oid', 'employment_role_oid');
                $table->renameColumn('end_reason_teacher_oid', 'end_reason_oid');
                $table->renameColumn('end_reason_teacher_other', 'end_reason_other');
                $table->renameColumn('end_reason_teacher_detail', 'end_reason_detail');
            });
        }
        if (Schema::hasTable('teacher_timetable')) {
            Schema::table('teacher_timetable', function (Blueprint $table) {
                $table->renameColumn('school_subject_oid', 'subject_oid');
                $table->renameColumn('school_subject_other', 'subject_other');
            });
        }
        if (Schema::hasTable('learner')) {
            Schema::table('learner', function (Blueprint $table) {
                $table->renameColumn('language_oid_strongest', 'strongest_language_oid');
                $table->renameColumn('disability_severity_oid_vision', 'disability_vision_oid');
                $table->renameColumn('disability_severity_oid_hearing', 'disability_hearing_oid');
                $table->renameColumn('disability_severity_oid_mobility', 'disability_mobility_oid');
                $table->renameColumn('disability_severity_oid_cognition', 'disability_cognition_oid');
                $table->renameColumn('disability_severity_oid_selfcare', 'disability_selfcare_oid');
                $table->renameColumn('disability_severity_oid_communication', 'disability_communication_oid');
            });
        }
        if (Schema::hasTable('media_photo')) {
            Schema::table('media_photo', function (Blueprint $table) {
                $table->renameColumn('base64_data', 'binary_data');
            });
        }
    }
};
