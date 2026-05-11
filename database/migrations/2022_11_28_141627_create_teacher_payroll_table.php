<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacherPayrollTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('teacher_payroll')) {
            Schema::create('teacher_payroll', function (Blueprint $table) {
                $table->string('uuid', 40)->primary();
                $table->integer('sid')->nullable();
                $table->string('first_name', 200)->nullable();
                $table->string('middle_name', 200)->nullable();
                $table->string('last_name', 200)->nullable();
                $table->string('sex', 10)->nullable();
                $table->date('date_of_birth')->nullable();
                $table->string('pin', 20)->nullable();
                $table->string('nin')->nullable();
                $table->string('nassit_number')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrentOnUpdate();
                $table->timestamp('deleted_at')->nullable();
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
        Schema::dropIfExists('teacher_payroll');
    }
}
