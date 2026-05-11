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
        Schema::create('android_db_backup', function (Blueprint $table) {
            $table->id();
            $table->string("username",100);
            $table->string("install_id",40);
            $table->string("app_version",5);
            $table->string("db_version",5);
            $table->string("path",200);
            $table->string("filename",100);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('android_db_backup');
    }
};
