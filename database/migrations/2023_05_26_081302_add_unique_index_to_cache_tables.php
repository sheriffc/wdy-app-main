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
        Schema::table('cache_attendance_by_school_date', function (Blueprint $table) {
            $table->unique(['date', 'school_uuid']);
        });
        Schema::table('cache_school_info', function (Blueprint $table) {
            $table->unique(['uuid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cache_attendance_by_school_date', function (Blueprint $table) {
            $table->dropUnique('cache_attendance_by_school_date_date_school_uuid_unique');
        });
        Schema::table('cache_school_info', function (Blueprint $table) {
            $table->dropUnique('cache_school_info_uuid_unique');
        });
    }
};
