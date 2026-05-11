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
        Schema::create('cache_school_info', function (Blueprint $table) {
            $table->string('uuid', 40)->primary();
            $table->string('name');
            $table->float('lat', 10, 0)->nullable();
            $table->float('lng', 10, 0)->nullable();
            $table->string('district_id')->nullable();
            $table->string('chiefdom_id')->nullable();
            $table->string('school_leader_name')->nullable();
            $table->string('school_leader_phone_number')->nullable();
            $table->string('teacher_total')->nullable();
            $table->string('teacher_female')->nullable();
            $table->string('teacher_male')->nullable();
            $table->string('teacher_profile_role')->nullable();
            $table->string('teacher_profile_start_date')->nullable();
            $table->string('teacher_profile_date_of_birth')->nullable();
            $table->string('teacher_profile_photo')->nullable();
            $table->string('teacher_profile_phone_number')->nullable();
            $table->string('teacher_profile_email')->nullable();
            $table->string('teacher_profile_address')->nullable();
            $table->string('teacher_profile_nin')->nullable();
            $table->string('teacher_profile_required_fields_complete')->nullable();
            $table->string('learners_total')->nullable();
            $table->string('learner_female')->nullable();
            $table->string('learner_male')->nullable();
            $table->string('maternal_status_not_complete')->nullable();
            $table->string('maternal_status_none')->nullable();
            $table->string('maternal_status_mother')->nullable();
            $table->string('maternal_status_pregnant')->nullable();
            $table->string('maternal_status_pregnant_mother')->nullable();
            $table->string('learner_profile_date_of_birth')->nullable();
            $table->string('learner_profile_sex')->nullable();
            $table->string('learner_profile_nin')->nullable();
            $table->string('learner_profile_admission_number')->nullable();
            $table->string('learner_profile_strongest_language')->nullable();
            $table->string('learner_profile_maternal_status')->nullable();
            $table->string('learner_profile_complete_needs_assessment')->nullable();
            $table->string('learner_profile_guardian_name')->nullable();
            $table->string('learner_profile_guardian_phone')->nullable();
            $table->string('learner_profile_guardian_address')->nullable();
            $table->string('learner_profile_required_fields_complete')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cache_school_info');
    }
};
