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

    public function up()
    {
        foreach (array_merge($this->uniTablesWithUuid,$this->uniTablesWithId) as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
//                    $table->timestamp('updated_at')->useCurrentOnUpdate()->change();
//                    $table->dropColumn($this->syncFieldName);
                    $table->dropColumn('synced_by');
                    $table->dropColumn('synced_by_install_id');
                    $table->dropIndex($tableName."_".$this->indicesSyncedAtInstallIdName);
//                    $table->dropIndex($tableName."_".$this->indicesSyncedAtPkName);
//                    $table->index(['updated_at'],"sync");
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
        foreach ($this->uniTablesWithUuid as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
//                    $table->timestamp('updated_at')->nullable(false)->change();
                    $table->after('synced_at', function ($table) {
//                        $table->timestamp($this->syncFieldName)->nullable(false);
                        $table->integer('synced_by')->nullable();
                        $table->string('synced_by_install_id', 40)->nullable();
                    });
//                    $table->dropIndex("sync");
                    $table->index($this->indicesSyncedAtInstallId, $tableName."_".$this->indicesSyncedAtInstallIdName);
//                    $table->index($this->indicesSyncedAtPk, $tableName."_".$this->indicesSyncedAtPkName);
                });
            }
        }
        //uni tables with id
        foreach ($this->uniTablesWithId as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
//                    $table->timestamp('updated_at')->nullable(false)->change();
                    $table->after('synced_at', function ($table) {
//                        $table->timestamp($this->syncFieldName)->nullable(false);
                        $table->integer('synced_by')->nullable();
                        $table->string('synced_by_install_id', 40)->nullable();
                    });
//                    $table->dropIndex("sync");
                    $table->index($this->indicesSyncedAtInstallId, $tableName."_".$this->indicesSyncedAtInstallIdName);
//                    $table->index($this->indicesSyncedAtPk, $tableName."_".$this->indicesSyncedAtPkName);
                });
            }
        }
    }
};
