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
        if(Schema::hasTable('teacher')) {
            Schema::table('teacher', function (Blueprint $table) {
                $table->index('pin', 'teacher_pin_index');
            });
        }

        if(Schema::hasTable('school')) {
            Schema::table('school', function (Blueprint $table) {
                $table->index('emis_id', 'school_emis_id_index');
                $table->index('emis_id', 'school_payroll_sid_index');
            });
        }

        if(Schema::hasTable('person')) {
            Schema::table('person', function (Blueprint $table) {
                $table->index('nin', 'person_nin_index');
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
        if(Schema::hasTable('teacher')) {
            Schema::table('teacher', function (Blueprint $table) {
                $table->dropIndex('teacher_pin_index');
            });
        }

        if(Schema::hasTable('school')) {
            Schema::table('school', function (Blueprint $table) {
                $table->dropIndex('school_emis_id_index');
                $table->dropIndex('school_payroll_sid_index');
            });
        }

        if(Schema::hasTable('person')) {
            Schema::table('person', function (Blueprint $table) {
                $table->dropIndex('person_nin_index');
            });
        }
    }
};
