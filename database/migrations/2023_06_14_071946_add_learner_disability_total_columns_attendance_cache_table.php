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
            $table->after('maternal_absent_none', function($table){
                $table->integer('disability_vision_learners')->nullable();
                $table->integer('disability_hearing_learners')->nullable();
                $table->integer('disability_mobility_learners')->nullable();
                $table->integer('disability_cognition_learners')->nullable();
                $table->integer('disability_selfcare_learners')->nullable();
                $table->integer('disability_communication_learners')->nullable();
            });

            $table->after('disability_other_condition_absent', function($table){
                $table->integer('disability_absent_disability_no_difficulty')->nullable();
                $table->integer('disability_absent_disability_none')->nullable();
            });
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
            $table->dropColumn('disability_vision_learners');
            $table->dropColumn('disability_hearing_learners');
            $table->dropColumn('disability_mobility_learners');
            $table->dropColumn('disability_cognition_learners');
            $table->dropColumn('disability_selfcare_learners');
            $table->dropColumn('disability_communication_learners');
            $table->dropColumn('disability_absent_disability_no_difficulty');
            $table->dropColumn('disability_absent_disability_none');
        });
    }
};
