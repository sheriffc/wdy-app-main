<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonFingerprintTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('person_fingerprint')) {
            Schema::create('person_fingerprint', function (Blueprint $table) {
                $table->string('uuid', 40)->primary();
                $table->string('person_uuid', 40)->nullable;
                $table->string('finger_position_oid', 10)->nullable();
                $table->text('fp_a_cbor')->nullable();
                $table->integer('fp_a_nfiq')->nullable();
                $table->text('fp_b_cbor')->nullable();
                $table->integer('fp_b_nfiq')->nullable();
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
        Schema::dropIfExists('person_fingerprint');
    }
}
