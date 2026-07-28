<?php

namespace App\Console\Commands;

use App\Services\LearnerIdService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixDuplicateLearnerIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'learners:fix-duplicate-ids {--apply : Actually write the changes (default is dry-run)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find duplicate learner_id values and renumber all but the earliest-created row in each group; also clears known non-conforming placeholder values';

    private const PLACEHOLDER_VALUES = ['ID_PLACEHOLDER', ''];

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');

        if (!$apply) {
            $this->warn('Running in dry-run mode. Pass --apply to commit changes.');
        }

        $this->clearPlaceholders($apply);
        $this->renumberDuplicates($apply);

        return self::SUCCESS;
    }

    private function clearPlaceholders(bool $apply): void
    {
        $rows = DB::table('learner')
            ->whereIn('learner_id', self::PLACEHOLDER_VALUES)
            ->get(['uuid', 'learner_id']);

        if ($rows->isEmpty()) {
            $this->line('No placeholder learner_id values found.');
            return;
        }

        $this->line("Found {$rows->count()} row(s) with a placeholder learner_id:");
        foreach ($rows as $row) {
            $this->line("  uuid={$row->uuid} learner_id=" . var_export($row->learner_id, true) . ' -> NULL');
        }

        if ($apply) {
            DB::table('learner')
                ->whereIn('learner_id', self::PLACEHOLDER_VALUES)
                ->update(['learner_id' => null]);
            $this->info("Cleared {$rows->count()} placeholder learner_id value(s).");
        }
    }

    private function renumberDuplicates(bool $apply): void
    {
        $duplicateIds = DB::table('learner')
            ->select('learner_id')
            ->whereNotNull('learner_id')
            ->whereNotIn('learner_id', self::PLACEHOLDER_VALUES)
            ->groupBy('learner_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('learner_id');

        if ($duplicateIds->isEmpty()) {
            $this->line('No duplicate learner_id groups found.');
            return;
        }

        $this->line("Found {$duplicateIds->count()} duplicate learner_id group(s).");

        // Cache the next-free sequence per (prefix, year) in memory rather than
        // re-querying after every row: in dry-run nothing is written yet, and even
        // under --apply this keeps 3+-member groups from all being assigned the
        // same "next available" number.
        $nextFree = [];

        foreach ($duplicateIds as $learnerId) {
            $parsed = LearnerIdService::parse($learnerId);
            if (!$parsed) {
                $this->error("  Could not parse '{$learnerId}' as {prefix}-{yy}-{seq}; skipping group.");
                continue;
            }

            $prefix = $parsed['prefix'];
            $year = $parsed['year'];
            $cacheKey = "$prefix|$year";
            if (!isset($nextFree[$cacheKey])) {
                $nextFree[$cacheKey] = LearnerIdService::nextAvailable($prefix, $year);
            }

            $rows = DB::table('learner')
                ->where('learner_id', $learnerId)
                ->orderBy('created_at')
                ->get(['uuid', 'created_at']);

            $this->line("  {$learnerId} ({$rows->count()} rows):");

            // Keep the earliest-created row untouched; renumber every later one.
            foreach ($rows->skip(1) as $row) {
                $newLearnerId = LearnerIdService::format($prefix, $year, $nextFree[$cacheKey]++);

                $this->line("    uuid={$row->uuid} created_at={$row->created_at} -> {$newLearnerId}");

                if ($apply) {
                    DB::table('learner')
                        ->where('uuid', $row->uuid)
                        ->update(['learner_id' => $newLearnerId]);
                }
            }
        }

        if ($apply) {
            $this->info('Duplicate learner_id groups renumbered.');
        }
    }
}
