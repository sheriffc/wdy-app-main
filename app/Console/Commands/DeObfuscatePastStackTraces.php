<?php

namespace App\Console\Commands;

use App\Common\Scripts\DeObfuscateStackTrace;
use Illuminate\Console\Command;

class DeObfuscatePastStackTraces extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deobfuscate:past';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        DeObfuscateStackTrace::processStackTraces();

        return Command::SUCCESS;
    }
}
