<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLearnerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('learner')) {
            Schema::create('learner', function (Blueprint $table) {
                $table->string('uuid', 40)->primary();
                $table->string('person_uuid', 40)->nullable();
                $table->string('learner_id', 50)->nullable();
                $table->string('strongest_language_oid', 100)->nullable();
                $table->string('maternal_status_oid', 100)->nullable();
//            $table->timestamp('maternal_status_updated_at')->nullable();
                $table->string('maternal_status_updated_at', 100)->nullable();
                $table->string('disability_vision_oid', 100)->nullable();
                $table->string('disability_hearing_oid', 100)->nullable();
                $table->string('disability_mobility_oid', 100)->nullable();
                $table->string('disability_cognition_oid', 100)->nullable();
                $table->string('disability_selfcare_oid', 100)->nullable();
                $table->string('disability_communication_oid', 100)->nullable();
                $table->string('disability_other_condition_oid', 100)->nullable();
                $table->string('guardian_person_uuid', 40)->nullable();
                $table->string('guardian_relation_to_learner_oid', 100)->nullable();
                $table->string('guardian_relation_to_learner_other')->nullable();
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
        Schema::dropIfExists('learner');
    }
}
