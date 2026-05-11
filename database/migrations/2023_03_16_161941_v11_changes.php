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
        if (Schema::hasTable('teacher_payroll')) {
            Schema::table('teacher_payroll', function (Blueprint $table) {
                $table->renameColumn('sid', 'school_sid');
            });
        }

        if (Schema::hasTable('teacher_payroll')) {
            Schema::table('teacher_payroll', function (Blueprint $table) {
                $table->after('school_sid', function ($table) {
                    $table->string('school_emis_id',100)->nullable();
                });
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
        //
    }
};
