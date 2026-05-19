<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_feeding_stock', function (Blueprint $table) {
            $table->string('uuid', 36)->primary();
            $table->string('school_uuid', 36)->index();
            $table->string('stock_month', 7); // yyyy-MM
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

            $table->unique(['school_uuid', 'stock_month'], 'school_feeding_stock_school_month_unique');
            $table->index(['synced_by_install_id', 'synced_at'], 'school_feeding_stock_install_id_synced_at');
            $table->index(['synced_at'], 'school_feeding_stock_synced_at');
            $table->index(['updated_at'], 'school_feeding_stock_updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_feeding_stock');
    }
};
