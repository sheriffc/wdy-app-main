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
        if(Schema::hasTable('teacher_payroll') && !Schema::hasColumn('teacher_payroll', 'yearmonth')){
            Schema::table('teacher_payroll', function (Blueprint $table) {
                $table->integer('yearmonth')->after('uuid');
                $table->unique('pin', 'tp_pin');
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
        if(Schema::hasTable('teacher_payroll') && Schema::hasColumn('teacher_payroll', 'yearmonth')){
            Schema::table('teacher_payroll', function (Blueprint $table) {
                $table->dropColumn('yearmonth');
                $table->dropIndex('tp_pin');
            });
        }
    }
};
