<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('geo')) {
            Schema::create('geo', function (Blueprint $table) {
                $table->id();
                $table->integer('parent_id');
                $table->string('name', 150);
                $table->string('type', 50)->nullable();
                $table->integer('fabinc_recordid')->nullable();
                $table->integer('display_order')->nullable();
                $table->tinyInteger('active')->default(1);
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrentOnUpdate();
                $table->timestamp('deleted_at')->nullable();
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
        Schema::dropIfExists('geo');
    }
}
