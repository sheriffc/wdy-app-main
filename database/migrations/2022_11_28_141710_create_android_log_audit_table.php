<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAndroidLogAuditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('android_log_audit')) {
            Schema::create('android_log_audit', function (Blueprint $table) {
                $table->integer('inc', true);
                $table->integer('id');
                $table->string('install_id', 40);
                $table->string('operation', 30)->nullable();
                $table->json('json_string')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->integer('created_by')->nullable();
                $table->timestamp('sync_at')->useCurrent();

                $table->unique(['install_id', 'id'], 'install log');
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
        Schema::dropIfExists('android_log_audit');
    }
}
