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
        if (Schema::hasTable('app_version') && !Schema::hasColumn('app_version', 'internal_notes')) {
            Schema::table('app_version', function (Blueprint $table) {
                $table->text('internal_notes')->nullable()->after('description');
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
        if (Schema::hasTable('app_version') && Schema::hasColumn('app_version', 'internal_notes')) {
            Schema::table('app_version', function (Blueprint $table) {
                $table->dropColumn('internal_notes');
            });
        }
    }
};
