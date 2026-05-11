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

    private array $indexesSyncedAtInstallId = ['school_uuid', 'synced_by_install_id', 'synced_at', 'uuid'];
    private string $indexesSyncedAtInstallIdName = "school_uuid_install_id_synced_at_uuid";
    private array $indexesSyncedAtPk = ['school_uuid', 'synced_at', 'uuid'];
    private string $indexesSyncedAtPkName = "school_uuid_synced_at_pk_uuid";

    //old
    private string $indicesSyncedAtInstallIdNameDrop = "install_id_synced_at";
    private array $indicesSyncedAtPk = ['synced_at'];
    private array $indicesSyncedAtInstallId = ['synced_by_install_id', 'synced_at'];
    private string $indicesSyncedAtPkNameDrop = "synced_at_pk";
    private array $schoolUuidTables = [
        'person_attendance',
        'school_group',
        'school_learner_admission',
        'teacher',
        'teacher_timetable',
    ];

    public function up()
    {
        //bi tables with id
        foreach ($this->schoolUuidTables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->dropIndex($tableName."_".$this->indicesSyncedAtInstallIdNameDrop);
                    $table->dropIndex($tableName."_".$this->indicesSyncedAtPkNameDrop);
                    $table->index($this->indexesSyncedAtInstallId, $tableName."_".$this->indexesSyncedAtInstallIdName);
                    $table->index($this->indexesSyncedAtPk, $tableName."_".$this->indexesSyncedAtPkName);
                });
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
        foreach (array_merge($this->biConfig) as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->dropIndex($tableName."_".$this->indexesSyncedAtInstallIdName);
                    $table->dropIndex($tableName."_".$this->indexesSyncedAtPkName);
                    $table->index($this->indicesSyncedAtInstallId, $tableName."_".$this->indicesSyncedAtInstallIdNameDrop);
                    $table->index($this->indicesSyncedAtPk, $tableName."_".$this->indicesSyncedAtPkNameDrop);
                });
            }
        }
    }
};
