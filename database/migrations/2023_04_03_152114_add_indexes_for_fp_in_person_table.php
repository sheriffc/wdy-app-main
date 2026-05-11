<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** to allow for checking on orphan fingerprints */

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('person')) {
            Schema::table('person', function (Blueprint $table) {
                $table->index('fp_li_uuid', 'person_fp_li_uuid');
                $table->index('fp_lt_uuid', 'person_fp_lt_uuid');
                $table->index('fp_ri_uuid', 'person_fp_ri_uuid');
                $table->index('fp_rt_uuid', 'person_fp_rt_uuid');
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
        if(Schema::hasTable('person')) {
            Schema::table('person', function (Blueprint $table) {
                $table->dropIndex('person_fp_li_uuid');
                $table->dropIndex('person_fp_lt_uuid');
                $table->dropIndex('person_fp_ri_uuid');
                $table->dropIndex('person_fp_rt_uuid');
            });
        }
    }
};
