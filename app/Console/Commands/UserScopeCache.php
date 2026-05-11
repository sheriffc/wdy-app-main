<?php

namespace App\Console\Commands;

use App\Http\Controllers\DteController;
use App\Common\Scripts\UserScopeCacheDt;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use
    DataTables;
use PDO;


class UserScopeCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:hashcache';

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

        $db = new DataTables\Database((new \App\Http\Controllers\DteController)->getDbSettings());
        UserScopeCacheDt::run();

        return Command::SUCCESS;
    }
}
