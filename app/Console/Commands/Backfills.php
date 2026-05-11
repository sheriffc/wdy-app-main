<?php

namespace App\Console\Commands;

use App\Scripts\Utils;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;


class Backfills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scripts:backfills';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populates the user hash and cache table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        exit();
    }
}
