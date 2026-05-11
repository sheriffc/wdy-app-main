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
                $table->integer('learners_screened_special_needs')->nullable();
                $table->integer('disability_learners_total')->nullable();
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
            $table->dropColumn('learners_screened_special_needs');
            $table->dropColumn('disability_learners_total');
        });
    }
};
