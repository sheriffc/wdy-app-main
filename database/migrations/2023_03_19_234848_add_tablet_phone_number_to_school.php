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
        if (Schema::hasTable('school') && !Schema::hasColumn('school', 'tablet_phone_number')) {
            Schema::table('school', function (Blueprint $table) {
                $table->string('tablet_phone_number', 50)->nullable()->after('rollout_batch');
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
        if (Schema::hasTable('school') && Schema::hasColumn('school', 'tablet_phone_number')) {
            Schema::table('school', function (Blueprint $table) {
                $table->dropColumn('tablet_phone_number');
            });
        }
    }
};
