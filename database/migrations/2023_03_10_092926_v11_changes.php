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
        if (Schema::hasTable('teacher_timetable')) {
            Schema::table('teacher_timetable', function (Blueprint $table) {
                $table->renameColumn('day_name', 'day_of_the_week_oid');
            });
        }

        if (Schema::hasTable('media_photo')) {
            Schema::table('media_photo', function (Blueprint $table) {
                $table->after('base64_data', function ($table) {
                    $table->smallInteger('display_orientation')->default(0);
                });
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
        if (Schema::hasTable('teacher_timetable')) {
            Schema::table('teacher_timetable', function (Blueprint $table) {
                $table->renameColumn('day_of_the_week_oid', 'day_name');
            });
        }

        if (Schema::hasTable('media_photo')) {
            Schema::table('media_photo', function (Blueprint $table) {
                $table->dropColumn('display_orientation');
            });
        }
    }
};
