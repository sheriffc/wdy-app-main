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
            $table->after('chiefdom_id', function($table){
                $table->string('district_name', 120)->nullable();
                $table->string('chiefdom_name', 120)->nullable();
            });
            $table->after('school_leader_phone_number', function($table){
                $table->integer('tablet_phone_number')->nullable();
                $table->integer('count_classrooms')->nullable();
                $table->string('last_teacher_submitted_date', 40)->nullable();
                $table->string('last_teacher_submitted_date_formated', 40)->nullable();
                $table->string('last_learner_submitted_date_formated', 40)->nullable();
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
            $table->dropColumn('district_name');
            $table->dropColumn('chiefdom_name');
            $table->dropColumn('tablet_phone_number');
            $table->dropColumn('count_classrooms');
            $table->dropColumn('last_teacher_submitted_date');
            $table->dropColumn('last_teacher_submitted_date_formated');
            $table->dropColumn('last_learner_submitted_date_formated');
        });
    }
};
