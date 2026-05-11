<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('user')) {
            Schema::create('user', function (Blueprint $table) {
                $table->id();
                $table->string('username')->unique();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('phone', 30)->nullable();
                $table->string('password', 100);
                $table->rememberToken();
                $table->string('user_role', 100)->nullable(); //permissions
                $table->integer('user_type_id')->default(0); //permissions
                $table->tinyInteger('mobile_access')->default(0); //scope
                $table->string('client_access_token', 20)->nullable(); //scope
                $table->timestamp('client_access_created_at')->nullable(); //scope
                $table->integer('scope_cache_id')->nullable(); //scope
                $table->string('scope_hash', 50)->nullable(); //scope
                $table->tinyInteger('active')->default(1);
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrentOnUpdate();
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
        Schema::dropIfExists('user');
    }
};
