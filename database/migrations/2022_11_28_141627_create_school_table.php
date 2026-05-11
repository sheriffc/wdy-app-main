<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('school')) {
            Schema::create('school', function (Blueprint $table) {
                $table->string('uuid', 40)->primary();
                $table->string('name');
                $table->string('education_level_oid', 50)->nullable();
                $table->string('emis_id')->nullable();
                $table->string('wideya_id')->nullable();
                $table->string('payroll_sid')->nullable();
                $table->integer('fabinc_recordid')->nullable();
                $table->string('district_id')->nullable();
                $table->string('chiefdom_id')->nullable();
                $table->string('section_name')->nullable();
                $table->string('town_name')->nullable();
                $table->text('address')->nullable();
                $table->string('district_office_uuid', 40)->nullable();
                $table->float('lat', 10, 0)->nullable();
                $table->float('lng', 10, 0)->nullable();
                $table->string('media_photo_uuid', 40)->nullable();
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
        Schema::dropIfExists('school');
    }
}
