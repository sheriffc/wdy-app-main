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
        if(Schema::hasTable('teacher_payroll')){
            Schema::table('teacher_payroll', function (Blueprint $table) {
                $table->string('pin', 20)->nullable(false)->change();
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
        if(Schema::hasTable('teacher_payroll')){
            Schema::table('teacher_payroll', function (Blueprint $table) {
                $table->string('pin', 20)->nullable()->change();
            });
        }
    }
};
