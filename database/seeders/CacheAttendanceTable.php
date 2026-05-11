<?php

namespace Database\Seeders;

use App\Queries\PopulateGeneralCacheTables;
use Illuminate\Database\Seeder;

class CacheAttendanceTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        PopulateGeneralCacheTables::initialPopulateCacheAttendanceTable();
    }
}
