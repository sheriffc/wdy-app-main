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
        Schema::table('cache_school_info', function($table)
        {
            $table->after('learner_profile_required_fields_complete', function($table){
                $table->integer('learners_disability_vision')->nullable();
                $table->integer('learners_disability_vision_severity_1')->nullable();
                $table->integer('learners_disability_vision_severity_2')->nullable();
                $table->integer('learners_disability_vision_severity_3')->nullable();
                $table->integer('learners_disability_hearing')->nullable();
                $table->integer('learners_disability_hearing_severity_1')->nullable();
                $table->integer('learners_disability_hearing_severity_2')->nullable();
                $table->integer('learners_disability_hearing_severity_3')->nullable();
                $table->integer('learners_disability_mobility')->nullable();
                $table->integer('learners_disability_mobility_severity_1')->nullable();
                $table->integer('learners_disability_mobility_severity_2')->nullable();
                $table->integer('learners_disability_mobility_severity_3')->nullable();
                $table->integer('learners_disability_cognition')->nullable();
                $table->integer('learners_disability_cognition_severity_1')->nullable();
                $table->integer('learners_disability_cognition_severity_2')->nullable();
                $table->integer('learners_disability_cognition_severity_3')->nullable();
                $table->integer('learners_disability_selfcare')->nullable();
                $table->integer('learners_disability_selfcare_severity_1')->nullable();
                $table->integer('learners_disability_selfcare_severity_2')->nullable();
                $table->integer('learners_disability_selfcare_severity_3')->nullable();
                $table->integer('learners_disability_communication')->nullable();
                $table->integer('learners_disability_communication_severity_1')->nullable();
                $table->integer('learners_disability_communication_severity_2')->nullable();
                $table->integer('learners_disability_communication_severity_3')->nullable();
                $table->integer('learners_condition_albinism')->nullable();
                $table->integer('learners_condition_epilepsy')->nullable();
                $table->integer('learners_condition_dwarfism')->nullable();
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
        Schema::table('cache_school_info', function($table)
        {
            $table->dropColumn('learners_disability_vision');
            $table->dropColumn('learners_disability_vision_severity_1');
            $table->dropColumn('learners_disability_vision_severity_2');
            $table->dropColumn('learners_disability_vision_severity_3');
            $table->dropColumn('learners_disability_hearing');
            $table->dropColumn('learners_disability_hearing_severity_1');
            $table->dropColumn('learners_disability_hearing_severity_2');
            $table->dropColumn('learners_disability_hearing_severity_3');
            $table->dropColumn('learners_disability_mobility');
            $table->dropColumn('learners_disability_mobility_severity_1');
            $table->dropColumn('learners_disability_mobility_severity_2');
            $table->dropColumn('learners_disability_mobility_severity_3');
            $table->dropColumn('learners_disability_cognition');
            $table->dropColumn('learners_disability_cognition_severity_1');
            $table->dropColumn('learners_disability_cognition_severity_2');
            $table->dropColumn('learners_disability_cognition_severity_3');
            $table->dropColumn('learners_disability_selfcare');
            $table->dropColumn('learners_disability_selfcare_severity_1');
            $table->dropColumn('learners_disability_selfcare_severity_2');
            $table->dropColumn('learners_disability_selfcare_severity_3');
            $table->dropColumn('learners_disability_communication');
            $table->dropColumn('learners_disability_communication_severity_1');
            $table->dropColumn('learners_disability_communication_severity_2');
            $table->dropColumn('learners_disability_communication_severity_3');
            $table->dropColumn('learners_condition_albinism');
            $table->dropColumn('learners_condition_epilepsy');
            $table->dropColumn('learners_condition_dwarfism');
        });
    }
};
