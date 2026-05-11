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
        //
        Schema::table('cache_school_info', function($table)
        {
            $table->integer('learners_removed_graduated')->nullable();
            $table->integer('learners_removed_transfer')->nullable();
            $table->integer('learners_removed_dropout_exams')->nullable();
            $table->integer('learners_removed_dropout_maternal')->nullable();
            $table->integer('learners_removed_dropout_other')->nullable();
            $table->integer('learners_removed_unknown')->nullable();
            $table->integer('learners_removed_duplicate')->nullable();
            $table->integer('learners_removed_mistake')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('cache_school_info', function($table)
        {
            $table->dropColumn('learners_removed_graduated');
            $table->dropColumn('learners_removed_transfer');
            $table->dropColumn('learners_removed_dropout_exams');
            $table->dropColumn('learners_removed_dropout_maternal');
            $table->dropColumn('learners_removed_dropout_other');
            $table->dropColumn('learners_removed_unknown');
            $table->dropColumn('learners_removed_duplicate');
            $table->dropColumn('learners_removed_mistake');
        });
    }
};
