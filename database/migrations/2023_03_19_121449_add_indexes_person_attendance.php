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
                $table->index(['person_uuid'],'pa_person');
                $table->index(['submitted', 'deleted_at', 'entity_type_oid', 'date', 'school_uuid'],'pa_subm_del_entity_date_school');
                $table->index(['submitted', 'deleted_at', 'entity_type_oid', 'school_uuid', 'date'],'pa_subm_del_entity_school_date');
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
                $table->dropIndex('pa_person');
                $table->dropIndex('pa_subm_del_entity_date_school');
                $table->dropIndex('pa_subm_del_entity_school_date');
            });
        }
    }
};
