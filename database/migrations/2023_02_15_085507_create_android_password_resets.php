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
        if (!Schema::hasTable('android_password_resets')) {
            Schema::create('android_password_resets', function (Blueprint $table) {
                $table->id();
                $table->string('username');
                $table->string('install_id', 50);
                $table->string('proposed_new_password_hash', 100);
                $table->string('status', 20); // pending, approved, cancelled, rejected, expired
                $table->text('remarks')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('expires_at'); // 7 day expiry, 14 days?
                $table->timestamp('updated_at')->useCurrentOnUpdate();
                $table->integer('updated_by');
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
        Schema::dropIfExists('android_password_resets');
    }
};
