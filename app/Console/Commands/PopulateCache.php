<?php

namespace App\Console\Commands;

use App\Queries\PopulateGeneralCacheTables;
use Illuminate\Console\Command;

class PopulateCache extends Command
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
    protected $signature = 'populate:cache {--today} {--rebuild}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate multiple caching tables';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $today = $this->option('today');
        $rebuild = $this->option('rebuild');

        $this->info('Command '.$this->signature.' started at ' . date('Y-m-d H:i:s'));

        if($today == 'true') {
            $this->info('updating cache for today');
            return $this->updateCacheForToday();
        } else if($rebuild == 'true') {
            $this->info('rebuilding cache');
            return $this->rebuildCache();
        } else {
            $this->info('updating cache for today');
            return $this->updateCacheForToday();
        }
    }

    function updateCacheForToday(): int {
        if($this->call('populate:school-info-cache') === Command::FAILURE){
            $this->error('problem populating school info cache');
            return Command::FAILURE;
        }

        if($this->call('populate:school-date-cache') === Command::FAILURE){
            $this->error('problem populating school date cache');
            return Command::FAILURE;
        }

        $this->info('finished updating cache for today');
        return Command::SUCCESS;
    }

    function rebuildCache(): int{
        if(PopulateGeneralCacheTables::populateCacheSchoolInfoTable() === Command::FAILURE){
            $this->error('problem rebuilding school info cache');
            return Command::FAILURE;
        }

        if(PopulateGeneralCacheTables::initialPopulateCacheAttendanceTable() === Command::SUCCESS){
            $this->info('finished rebuilding cache');
            return Command::SUCCESS;
        } else {
            $this->error('problem rebuilding attendance cache');
            return Command::FAILURE;
        }
    }
}
