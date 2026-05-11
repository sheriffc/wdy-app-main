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
        if (Schema::hasTable('teacher')) {
            Schema::table('teacher', function (Blueprint $table) {
                $table->index(['school_uuid','person_uuid'], 'teacher_idx_school_uuid_person_uuid');
            });
        }
        if (Schema::hasTable('person_fingerprint')) {
            Schema::table('person_fingerprint', function (Blueprint $table) {
                $table->dropIndex('person_fingerprint_synced_at_pk');
                $table->index(['person_uuid','synced_at', 'uuid'], 'person_fingerprint_initial_sync');
                $table->dropIndex('person_fingerprint_install_id_synced_at');
                $table->index(['person_uuid','synced_by_install_id','synced_at', 'uuid'], 'person_fingerprint_incremental_sync');
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
        if (Schema::hasTable('teacher')) {
            Schema::table('teacher', function (Blueprint $table) {
                $table->dropIndex('teacher_idx_school_uuid_person_uuid');
            });
        }
        if (Schema::hasTable('person_fingerprint')) {
            Schema::table('person_fingerprint', function (Blueprint $table) {
                $table->dropIndex('person_fingerprint_initial_sync');
                $table->index(['synced_at'], 'person_fingerprint_synced_at_pk');
                $table->dropIndex('person_fingerprint_incremental_sync');
                $table->index(['synced_by_install_id','synced_at'], 'person_fingerprint_install_id_synced_at');
            });
        }
    }
};
