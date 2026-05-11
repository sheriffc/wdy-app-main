<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Adds the sync_at field to all tables that sync.
     *  This field captures the *server* time that the record is affected,
     *  to enable clients to sync based on order of receipt rather than
     *  time of change.  Requires client to store server time of last sync
     *  and server to provide an end point to retrieve server time.
     */

    /**
     * Run the migrations.
     *
     * @return void
     */
    private string $syncFieldName = 'synced_at';
    private string $indicesSyncedAtInstallIdName = "install_id_synced_at";
    private array $indicesSyncedAtInstallId = ['synced_by_install_id', 'synced_at'];
    private string $indicesSyncedAtPkName = "synced_at_pk";
    private array $indicesSyncedAtPk = ['synced_at'];
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
        //uni tables uuid
        foreach ($this->uniTablesWithUuid as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->timestamp('updated_at')->nullable(false)->change();
                    $table->after('deleted_at', function ($table) {
                        $table->timestamp($this->syncFieldName)->nullable(false);
                        $table->integer('synced_by')->nullable();
                        $table->string('synced_by_install_id', 40)->nullable();
                    });
                    $table->dropIndex("sync");
                    $table->index($this->indicesSyncedAtInstallId, $tableName."_".$this->indicesSyncedAtInstallIdName);
                    $table->index($this->indicesSyncedAtPk, $tableName."_".$this->indicesSyncedAtPkName);
                });
            }
        }
        //uni tables with id
        foreach ($this->uniTablesWithId as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->timestamp('updated_at')->nullable(false)->change();
                    $table->after('deleted_at', function ($table) {
                        $table->timestamp($this->syncFieldName)->nullable(false);
                        $table->integer('synced_by')->nullable();
                        $table->string('synced_by_install_id', 40)->nullable();
                    });
                    $table->dropIndex("sync");
                    $table->index($this->indicesSyncedAtInstallId, $tableName."_".$this->indicesSyncedAtInstallIdName);
                    $table->index($this->indicesSyncedAtPk, $tableName."_".$this->indicesSyncedAtPkName);
                });
            }
        }

        //bi tables with id
        foreach ($this->biConfig as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->timestamp('updated_at')->nullable(false)->change();
                    $table->after('deleted_by', function ($table) {
                        $table->timestamp($this->syncFieldName)->nullable(false);
                        $table->integer('synced_by')->nullable();
                        $table->string('synced_by_install_id', 40)->nullable();
                    });
                    $table->dropIndex("sync");
                    $table->index($this->indicesSyncedAtInstallId, $tableName."_".$this->indicesSyncedAtInstallIdName);
                    $table->index($this->indicesSyncedAtPk, $tableName."_".$this->indicesSyncedAtPkName);
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
        //uni tables uuid
        //uni tables with id
        foreach (array_merge($this->uniTablesWithUuid,$this->uniTablesWithId, $this->biConfig) as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->timestamp('updated_at')->useCurrentOnUpdate()->change();
                    $table->dropColumn($this->syncFieldName);
                    $table->dropColumn('synced_by');
                    $table->dropColumn('synced_by_install_id');
                    $table->dropIndex($tableName."_".$this->indicesSyncedAtInstallIdName);
                    $table->dropIndex($tableName."_".$this->indicesSyncedAtPkName);
                    $table->index(['updated_at'],"sync");
                });
            }
        }
    }
};
