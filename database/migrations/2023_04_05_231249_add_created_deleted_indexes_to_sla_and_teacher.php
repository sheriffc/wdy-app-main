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
        if(Schema::hasTable('school_learner_admission')) {
            Schema::table('school_learner_admission', function (Blueprint $table) {
                $table->index(['created_at', 'deleted_at', 'school_uuid'], 'school_learner_admission_created_deleted_school');
            });
        }

        if(Schema::hasTable('teacher')) {
            Schema::table('teacher', function (Blueprint $table) {
                $table->index(['created_at', 'deleted_at', 'school_uuid'], 'teacher_created_deleted_school');
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
        if(Schema::hasTable('school_learner_admission')) {
            Schema::table('school_learner_admission', function (Blueprint $table) {
                $table->dropIndex('school_learner_admission_created_deleted_school');
            });
        }

        if(Schema::hasTable('teacher')) {
            Schema::table('teacher', function (Blueprint $table) {
                $table->dropIndex('teacher_created_deleted_school');
            });
        }
    }
};
