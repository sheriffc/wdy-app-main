<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistrictOfficeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('district_office')) {
            Schema::create('district_office', function (Blueprint $table) {
                $table->string('uuid', 40)->primary();
                $table->string('name', 100);
                $table->integer('district_id')->nullable();
                $table->string('leader_id', 40)->nullable();
                $table->float('lat', 10, 0)->nullable();
                $table->float('lng', 10, 0)->nullable();
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
        Schema::dropIfExists('district_office');
    }
}
