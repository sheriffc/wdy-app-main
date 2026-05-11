<?php

namespace App\Console\Commands;

use App\Queries\PopulateGeneralCacheTables;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PopulateAttendanceBySchoolDateCache extends Command
{
    /**
     * - input of school uuid and date
     * wider commands to rebuild cache
     * - rebuild for all schools on a day
     * - rebuild for a date range
     */

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:school-date-cache {--school_uuid=} {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate school date cache tables';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $schoolUuid = $this->option('school_uuid') ?? null;

        // check if school uuid is valid
        if($schoolUuid) {
            if (!DB::select('select uuid from school where uuid = ?', [$schoolUuid])) {
                Log::error('Invalid school uuid passed to populate:cache command');
                return Command::FAILURE;
            }
        }

        $date = $this->option('date');

        // check if date is valid
        if($date) {
            try {
                Carbon::parse($date);
            } catch (\Exception $e) {
                Log::error('Invalid date passed to populate:cache command, error:' . $e);
                return Command::FAILURE;
            }
        }

        if($schoolUuid && $date){
            return $this->updateCacheForSchoolForDay($schoolUuid, $date);
        } elseif($schoolUuid) {
            return $this->updateCacheForSchoolForDay($schoolUuid, Carbon::now()->toDateString());
        } elseif($date) {
            return $this->updateCacheForDay($date);
        } else {
            return $this->updateCacheForToday();
        }
    }

    public function updateCacheForToday(): int {
        if(PopulateGeneralCacheTables::populateCacheAttendanceByDate(Carbon::now()->toDateString())){
            return Command::SUCCESS;
        } else {
            return Command::FAILURE;
        }
    }

     public function updateCacheForSchoolForDay($schoolUuid, $date): int
     {
        if(PopulateGeneralCacheTables::populateCacheAttendanceBySchoolAndDate($schoolUuid, $date)){
            return Command::SUCCESS;
        } else {
            return Command::FAILURE;
        }
     }

     public function updateCacheForDay($date): int
     {
         if(PopulateGeneralCacheTables::populateCacheAttendanceByDate($date)){
             return Command::SUCCESS;
         } else {
             return Command::FAILURE;
         }
     }

}
