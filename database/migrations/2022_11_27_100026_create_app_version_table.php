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
        if (!Schema::hasTable('app_version')) {
            Schema::create('app_version', function (Blueprint $table) {
                $table->id();
                $table->tinyInteger("force_update")->nullable()->default(0);
                $table->tinyInteger("latest_version")->nullable()->default(0);
                $table->string("version_code", 255)->nullable()->default(null);
                $table->string("version_name", 255)->nullable()->default(null);
                $table->string("db_schema", 255)->nullable()->default(null);
                $table->string("uri", 255)->nullable()->default(null);
                $table->string("filename", 255)->nullable()->default(null);
                $table->text("description")->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->default(null);
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
        Schema::dropIfExists('app_version');
    }
};
