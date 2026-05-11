<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('teacher')) {
            Schema::create('teacher', function (Blueprint $table) {
                $table->string('uuid', 40)->primary();
                $table->string('person_uuid', 40)->nullable();
                $table->string('school_uuid', 40)->nullable();
                $table->string('employment_status_oid', 100)->nullable();
                $table->string('pin', 40)->nullable();
                $table->string('employment_role_oid', 100)->nullable();
                $table->string('nassit_number', 40)->nullable();
                $table->string('tsc_licence_id', 40)->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->string('end_reason_oid', 100)->nullable();
                $table->text('end_reason_other')->nullable();
                $table->text('end_reason_detail')->nullable();
                $table->tinyInteger('active')->default(1);
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
        Schema::dropIfExists('teacher');
    }
}
