<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learner_performance', function (Blueprint $table) {
            $table->string('uuid', 36)->primary();
            $table->string('school_uuid', 36)->index();
            $table->string('school_group_uuid', 36)->index();
            $table->string('learner_uuid', 36)->index();
            $table->string('subject_oid', 50)->index();
            $table->smallInteger('academic_year');
            $table->string('term_oid', 30);
            $table->decimal('assessment_1_score', 7, 2)->nullable();
            $table->decimal('assessment_2_score', 7, 2)->nullable();
            $table->decimal('max_score', 7, 2)->default(100);
            $table->dateTime('created_at');
            $table->integer('created_by');
            $table->dateTime('updated_at')->index();
            $table->integer('updated_by');
            $table->dateTime('deleted_at')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->tinyInteger('sync_flag')->default(1);
            $table->dateTime('synced_at')->nullable()->index();
            $table->integer('synced_by')->nullable();
            $table->string('synced_by_install_id', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learner_performance');
    }
};
