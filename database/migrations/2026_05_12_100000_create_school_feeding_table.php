<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_feeding', function (Blueprint $table) {
            $table->string('uuid', 36)->primary();
            $table->string('school_uuid', 36)->index();
            $table->tinyInteger('receives_feeding')->default(0);
            $table->string('supply_period_oid', 30)->nullable();
            $table->date('received_at')->nullable();
            $table->string('supplied_by_oid', 30)->nullable();
            $table->string('supplied_by_other', 100)->nullable();
            $table->integer('qty_rice')->nullable();
            $table->integer('qty_beans')->nullable();
            $table->integer('qty_gari')->nullable();
            $table->integer('qty_veg_oil')->nullable();
            $table->integer('qty_salt')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->integer('created_by');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->integer('updated_by');
            $table->timestamp('deleted_at')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->integer('synced_by')->nullable();
            $table->string('synced_by_install_id', 40)->nullable();

            $table->index(['synced_by_install_id', 'synced_at'], 'school_feeding_install_id_synced_at');
            $table->index(['synced_at'], 'school_feeding_synced_at_pk');
            $table->index(['updated_at'], 'school_feeding_updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_feeding');
    }
};
