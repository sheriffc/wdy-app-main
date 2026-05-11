<?php

namespace App\Console\Commands;

use App\Queries\PopulateGeneralCacheTables;
use Illuminate\Console\Command;

class PopulateSchoolInfoCache extends Command
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
    protected $signature = 'populate:school-info-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate school info cache tables';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return $this->updateCacheForToday();
    }

    public function updateCacheForToday(): int {
        if(!PopulateGeneralCacheTables::populateCacheSchoolInfoTable()){
            return Command::FAILURE;
        } else {
            return Command::SUCCESS;
        }
    }
}
