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
        if (Schema::hasTable('school')) {
            Schema::table('school', function (Blueprint $table) {
                $table->after('payroll_sid', function ($table) {
                    $table->string('waec_id', 100)->nullable();
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
        Schema::table('school', function (Blueprint $table) {
            if (Schema::hasTable('school')) {
                Schema::table('school', function (Blueprint $table) {
                    $table->dropColumn('waec_id');
                });
            }
        });
    }
};
