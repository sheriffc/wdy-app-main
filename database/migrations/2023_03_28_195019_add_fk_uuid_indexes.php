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
        if(Schema::hasTable('school_group')) {
            Schema::table('school_group', function (Blueprint $table) {
                $table->index('school_uuid', 'sg_school_uuid');
                $table->index('teacher_uuid', 'sg_teacher_uuid');
            });
        }

        if(Schema::hasTable('school_learner_admission')) {
           Schema::table('school_learner_admission', function (Blueprint $table) {
               $table->index('learner_uuid', 'sla_learner_uuid');
               $table->index('school_uuid', 'sla_school_uuid');
           });
       }

        if(Schema::hasTable('school_learner_enrolment')) {
            Schema::table('school_learner_enrolment', function (Blueprint $table) {
                $table->index('learner_uuid', 'sle_learner_uuid');
                $table->index('school_group_uuid', 'sle_school_group_uuid');
            });
        }

        if(Schema::hasTable('teacher_payroll')) {
            Schema::table('teacher_payroll', function (Blueprint $table) {
                $table->index('school_sid', 'tp_school_sid');
                $table->index('school_emis_id', 'tp_school_emis_id');
            });
        }

        if(Schema::hasTable('teacher_timetable')) {
            Schema::table('teacher_timetable', function (Blueprint $table) {
                $table->index('teacher_uuid', 'tt_teacher_uuid');
                $table->index('school_uuid', 'tt_school_uuid');
                $table->index('school_group_uuid', 'tt_school_group_uuid');
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
        if(Schema::hasTable('school_group')) {
            Schema::table('school_group', function (Blueprint $table) {
                $table->dropIndex('sg_school_uuid');
                $table->dropIndex('sg_teacher_uuid');
            });
        }

        if(Schema::hasTable('school_learner_admission')) {
            Schema::table('school_learner_admission', function (Blueprint $table) {
                $table->dropIndex('sla_learner_uuid');
                $table->dropIndex('sla_school_uuid');
            });
        }

        if(Schema::hasTable('school_learner_enrolment')) {
            Schema::table('school_learner_enrolment', function (Blueprint $table) {
                $table->dropIndex('sle_learner_uuid');
                $table->dropIndex('sle_school_group_uuid');
            });
        }

        if(Schema::hasTable('teacher_payroll')) {
            Schema::table('teacher_payroll', function (Blueprint $table) {
                $table->dropIndex('tp_school_sid');
                $table->dropIndex('tp_school_emis_id');
            });
        }

        if(Schema::hasTable('teacher_timetable')) {
            Schema::table('teacher_timetable', function (Blueprint $table) {
                $table->dropIndex('tt_teacher_uuid');
                $table->dropIndex('tt_school_uuid');
                $table->dropIndex('tt_school_group_uuid');
            });
        }
    }
};
