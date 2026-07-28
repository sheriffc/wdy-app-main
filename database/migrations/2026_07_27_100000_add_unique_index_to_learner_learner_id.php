<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddUniqueIndexToLearnerLearnerId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $duplicateGroups = DB::table('learner')
            ->select('learner_id')
            ->whereNotNull('learner_id')
            ->where('learner_id', '<>', '')
            ->groupBy('learner_id')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        $blankCount = DB::table('learner')->where('learner_id', '')->count();

        if ($duplicateGroups > 0 || $blankCount > 0) {
            throw new \RuntimeException(
                "Cannot add unique index on learner.learner_id: found {$duplicateGroups} duplicate group(s) "
                . "and {$blankCount} empty-string value(s). Run `php artisan learners:fix-duplicate-ids --apply` first."
            );
        }

        Schema::table('learner', function (Blueprint $table) {
            $table->unique('learner_id', 'learner_learner_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('learner', function (Blueprint $table) {
            $table->dropUnique('learner_learner_id_unique');
        });
    }
}
