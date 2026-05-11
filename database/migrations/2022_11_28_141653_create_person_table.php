<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('person')) {
            Schema::create('person', function (Blueprint $table) {
                $table->string('uuid', 40)->primary();
                $table->string('first_name', 150)->nullable();
                $table->string('middle_name', 150)->nullable();
                $table->string('last_name', 150)->nullable();
                $table->string('sex_oid', 100)->nullable();
                $table->date('date_of_birth')->nullable();
                $table->string('nin', 50)->nullable();
                $table->string('portrait_uuid', 40)->nullable();
                $table->string('phone_1', 50)->nullable();
                $table->string('phone_2', 50)->nullable();
                $table->string('email', 150)->nullable();
                $table->text('address')->nullable();
                $table->string('fp_lt_uuid', 40)->nullable();
                $table->string('fp_li_uuid', 40)->nullable();
                $table->string('fp_rt_uuid', 40)->nullable();
                $table->string('fp_ri_uuid', 40)->nullable();
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
        Schema::dropIfExists('person');
    }
}
