<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolLearnerAdmissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('school_learner_admission')) {
            Schema::create('school_learner_admission', function (Blueprint $table) {
                $table->string('uuid', 40)->primary();
                $table->string('school_uuid', 40)->nullable();
                $table->string('learner_uuid', 40)->nullable();
                $table->integer('admission_number')->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->string('end_reason_oid', 100)->nullable();
                $table->text('end_reason_other')->nullable();
                $table->text('end_reason_detail')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->integer('created_by');
                $table->timestamp('updated_at')->useCurrentOnUpdate();
                $table->integer('updated_by');
                $table->timestamp('deleted_at')->nullable();
                $table->integer('deleted_by')->nullable();
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
        Schema::dropIfExists('school_learner_admission');
    }
}
