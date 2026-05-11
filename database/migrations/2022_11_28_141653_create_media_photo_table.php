<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaPhotoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('media_photo')) {
            Schema::create('media_photo', function (Blueprint $table) {
                $table->string('uuid', 40)->primary();
                $table->string('ref_uuid', 40)->nullable();
                $table->mediumText('binary_data')->nullable();
                $table->tinyInteger('active')->default(1);
                $table->timestamp('created_at')->useCurrent();
                $table->integer('created_by');
                $table->timestamp('updated_at')->useCurrentOnUpdate();
                $table->integer('updated_by');
                $table->timestamp('deleted_at')->nullable();
                $table->integer('deleted_by')->nullable();
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
        Schema::dropIfExists('media_photo');
    }
}
