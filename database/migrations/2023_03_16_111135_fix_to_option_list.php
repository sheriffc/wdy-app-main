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
    public function up()
    {
        if (Schema::hasTable('option_list')) {
            DB::table('option_list')
                ->where('list_name', 'teacher_timetable_days')
                ->update(['list_name'=>'day_of_the_week']);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('option_list')) {
            DB::table('option_list')
                ->where('list_name', 'day_of_the_week')
                ->update(['list_name'=>'teacher_timetable_days']);
        }
    }
};
