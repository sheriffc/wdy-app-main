<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanPreCreatedAttendance extends Command
{
    protected $signature = 'attendance:clean-pre-created
                            {--date= : Specific date to clean (yyyy-mm-dd). Defaults to today.}
                            {--dry-run : Show count of affected records without deleting.}';

    protected $description = 'Delete person_attendance records that were created before their attendance date (pre-created by the No School feature)';

    public function handle(): int
    {
        $date = $this->option('date') ?? now()->toDateString();
        $dryRun = $this->option('dry-run');

        $baseQuery = DB::table('person_attendance')
            ->whereNull('deleted_at')
            ->whereDate('date', '>=', $date)
            ->whereRaw('DATE(created_at) < date');

        $count = $baseQuery->count();

        if ($dryRun) {
            $this->info("Dry run: {$count} pre-created attendance record(s) would be deleted for dates >= {$date}.");
            return Command::SUCCESS;
        }

        if ($count === 0) {
            $this->info("No pre-created attendance records found for dates >= {$date}.");
            return Command::SUCCESS;
        }

        DB::table('person_attendance')
            ->whereNull('deleted_at')
            ->whereDate('date', '>=', $date)
            ->whereRaw('DATE(created_at) < date')
            ->delete();

        $this->info("Deleted {$count} pre-created attendance record(s) for dates >= {$date}.");
        return Command::SUCCESS;
    }
}
