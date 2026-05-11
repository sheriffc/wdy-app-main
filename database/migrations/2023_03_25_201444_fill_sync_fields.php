<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    private array $uniTablesWithUuid = [
        'district_office',
        'school_academic_year',
        'teacher_payroll',
    ];
    private array $uniTablesWithId = [
        'option_list',
        'option_list_link',
        'geo',
    ];
    private array $biConfig = [
        'learner',
        'person',
        'person_attendance',
        'school',
        'school_group',
        'school_learner_admission',
        'school_learner_enrolment',
        'teacher',
        'person_fingerprint',
        'media_photo',
        'teacher_timetable',
    ];
    public function up()
    {
        foreach (array_merge($this->uniTablesWithUuid,$this->uniTablesWithId, $this->biConfig) as $tableName) {
            if (Schema::hasTable($tableName)) {
                DB::statement("UPDATE $tableName SET synced_at = NOW(), synced_by = '-1', synced_by_install_id = 'server'");
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach (array_merge($this->uniTablesWithUuid,$this->uniTablesWithId, $this->biConfig) as $tableName) {
            if (Schema::hasTable($tableName)) {
                DB::statement("UPDATE $tableName SET synced_at = NULL, synced_by = NULL, synced_by_install_id = NULL");
            }
        }
    }
};
