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
    private array $updatedAt = [
        'teacher_payroll',
        'district_office',
        'school_academic_year',
        'option_list',
        'geo',
        'learner',
        'person',
        'school',
        'media_photo',
    ];
    private array $schoolUuid_updatedAt = [
        'person_attendance',
        'teacher',
        'school_learner_admission',
        'school_group',
        'teacher_timetable',
    ];

    private string $indexName = 'sync';
    public function up()
    {
        //just need 'updated_at' indexed for sync
        foreach($this->updatedAt as $tableName){
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->index(['updated_at'],$this->indexName);
                });
            }
        }

        //school uuid and updated at
        foreach($this->schoolUuid_updatedAt as $tableName){
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->index(['school_uuid', 'updated_at'],$this->indexName);
                });
            }
        }

        //others
        if (Schema::hasTable('school_learner_enrolment')) {
            Schema::table('school_learner_enrolment', function (Blueprint $table) {
                $table->index(['school_group_uuid', 'updated_at'],$this->indexName);
            });
        }
        if (Schema::hasTable('person_fingerprint')) {
            Schema::table('person_fingerprint', function (Blueprint $table) {
                $table->index(['person_uuid', 'updated_at'],$this->indexName);
            });
        }

        if (Schema::hasTable('person')) {
            Schema::table('person', function (Blueprint $table) {
                $table->index(['portrait_uuid'],'person_portrait_uuid_index');
            });
        }

        if (Schema::hasTable('teacher')) {
            Schema::table('teacher', function (Blueprint $table) {
                $table->index('person_uuid','teacher_person_uuid_index');
                $table->index('school_uuid','teacher_school_uuid_index');
            });
        }

        if (Schema::hasTable('learner')) {
            Schema::table('learner', function (Blueprint $table) {
                $table->index('person_uuid','learner_person_uuid_index');
                $table->index('guardian_person_uuid','learner_guardian_person_uuid_index');
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
        //just need 'updated_at' indexed for sync
        foreach($this->updatedAt as $tableName){
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropIndex($this->indexName);
                });
            }
        }

        //school uuid and updated at
        foreach($this->schoolUuid_updatedAt as $tableName){
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropIndex($this->indexName);
                });
            }
        }

        //others
        if (Schema::hasTable('school_learner_enrolment')) {
            Schema::table('school_learner_enrolment', function (Blueprint $table) {
                $table->dropIndex($this->indexName);
            });
        }
        if (Schema::hasTable('person_fingerprint')) {
            Schema::table('person_fingerprint', function (Blueprint $table) {
                $table->dropIndex($this->indexName);
            });
        }

        if (Schema::hasTable('person')) {
            Schema::table('person', function (Blueprint $table) {
                $table->dropIndex('person_portrait_uuid_index');
            });
        }

        if (Schema::hasTable('teacher')) {
            Schema::table('teacher', function (Blueprint $table) {
                $table->dropIndex('teacher_person_uuid_index');
                $table->dropIndex('teacher_school_uuid_index');
            });
        }

        if (Schema::hasTable('learner')) {
            Schema::table('learner', function (Blueprint $table) {
                $table->dropIndex('learner_person_uuid_index');
                $table->dropIndex('learner_guardian_person_uuid_index');
            });
        }
    }
};
