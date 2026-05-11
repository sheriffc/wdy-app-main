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
        Schema::table('app_version', function (Blueprint $table) {
            $table->dropColumn('latest_version');
            $table->tinyInteger('active')->default(0)->after('version_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_version', function (Blueprint $table) {
            $table->dropColumn('active');
            $table->tinyInteger('latest_version')->default(0)->after('version_name');
        });
    }
};
