<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//         $schedule->command('send:androidstacktracereport')->hourly()->between('6:00', '20:00');
         $schedule->command('send:androidstacktracereport')->dailyAt('6:00');
         $schedule->command('populate:cache')
             ->dailyAt('23:00')
             ->appendOutputTo(storage_path('logs/scheduler.log'));;
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
