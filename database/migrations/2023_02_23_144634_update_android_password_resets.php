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
        if (Schema::hasTable('android_password_resets')) {
            Schema::table('android_password_resets', function (Blueprint $table) {
                //add these new cols
                $table->after('proposed_new_password_hash', function ($table) {
                    $table->string('old_password_hash', 100)->nullable(); // save the old password incase we need to restore
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
        if (Schema::hasTable('android_password_resets')) {
            Schema::create('android_password_resets', function (Blueprint $table) {
                $table->dropColumn('old_password_hash');
            });
        }
    }
};
