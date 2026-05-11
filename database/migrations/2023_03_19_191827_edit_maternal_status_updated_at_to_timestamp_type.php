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
        if(Schema::hasTable('learner')){
            Schema::table('learner', function (Blueprint $table) {
                $table->timestamp('maternal_status_updated_at')->nullable()->change();
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
        if(Schema::hasTable('learner')){
            Schema::table('learner', function (Blueprint $table) {
                $table->string('maternal_status_updated_at', 100)->nullable()->change();
            });
        }
    }
};
