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
        if (Schema::hasTable('user')) {
            Schema::table('user', function (Blueprint $table) {
                // phone not nullable
                $table->string('phone', 30)->nullable(false)->change();
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
        if (Schema::hasTable('user')) {
            Schema::table('user', function (Blueprint $table) {
                $table->string('phone', 30)->nullable()->change();
            });
        }
    }
};
