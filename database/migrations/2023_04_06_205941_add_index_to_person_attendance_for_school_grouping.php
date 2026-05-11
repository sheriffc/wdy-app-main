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
        if (Schema::hasTable('person_attendance')) {
            Schema::table('person_attendance', function (Blueprint $table) {
                $table->index(['school_uuid', 'submitted', 'deleted_at', 'entity_type_oid', 'date', 'attendance_status_oid'], 'pa_school_subm_del_entity_date_attendance');
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
        if (Schema::hasTable('person_attendance')) {
            Schema::table('person_attendance', function (Blueprint $table) {
                $table->dropIndex('pa_school_subm_del_entity_date_attendance');
            });
        }
    }
};
