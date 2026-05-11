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
            $table->after('learners_reported', function($table){
                $table->integer('learners_male')->nullable();
                $table->integer('learners_female')->nullable();
                $table->integer('learners_present')->nullable();
                $table->integer('learners_absent')->nullable();
                $table->integer('learners_male_present')->nullable();
                $table->integer('learners_female_present')->nullable();
                $table->integer('learners_male_absent')->nullable();
                $table->integer('learners_female_absent')->nullable();
            });
           
            $table->after('learners_pm_absent', function($table){
                $table->integer('maternal_learners_mothers')->nullable();
                $table->integer('maternal_learners_pregnant')->nullable();
                $table->integer('maternal_learners_pregnant_mother')->nullable();
                $table->integer('maternal_learners_status_none')->nullable();
            });

            $table->after('maternal_pm_absent_pregnant_mother', function($table){
                $table->integer('maternal_present_none')->nullable();
                $table->integer('maternal_absent_none')->nullable();
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
                $table->dropColumn('learners_male');
                $table->dropColumn('learners_female');
                $table->dropColumn('learners_present');
                $table->dropColumn('learners_absent');
                $table->dropColumn('learners_male_present');
                $table->dropColumn('learners_female_present');
                $table->dropColumn('learners_male_absent');
                $table->dropColumn('learners_female_absent');
                $table->dropColumn('maternal_learners_mothers');
                $table->dropColumn('maternal_learners_pregnant');
                $table->dropColumn('maternal_learners_pregnant_mother');
                $table->dropColumn('maternal_learners_status_none');
                $table->dropColumn('maternal_present_none');
                $table->dropColumn('maternal_absent_none');
            
        });
    }
};
