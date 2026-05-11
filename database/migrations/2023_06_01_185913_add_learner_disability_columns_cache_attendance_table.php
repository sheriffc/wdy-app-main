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
        //
        Schema::table('cache_attendance_by_school_date', function($table)
        {
            $table->integer('disability_vision_absent')->nullable();
            $table->integer('disability_hearing_absent')->nullable();
            $table->integer('disability_mobility_absent')->nullable();
            $table->integer('disability_cognition_absent')->nullable();
            $table->integer('disability_selfcare_absent')->nullable();
            $table->integer('disability_communication_absent')->nullable();
            $table->integer('disability_other_condition_absent')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('cache_attendance_by_school_date', function($table)
        {
            $table->dropColumn('disability_vision_absent');
            $table->dropColumn('disability_hearing_absent');
            $table->dropColumn('disability_mobility_absent');
            $table->dropColumn('disability_cognition_absent');
            $table->dropColumn('disability_selfcare_absent');
            $table->dropColumn('disability_communication_absent');
            $table->dropColumn('disability_other_condition_absent');
        });
    }
};
