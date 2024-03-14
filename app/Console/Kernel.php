<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;


class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\CompressImageTask::class,
         Commands\SendEmailTask::class,
          Commands\WeeklyDigestTask::class,
          Commands\TrasncodeVideoTask::class,
          Commands\DailyNotificationMailTask::class,
          Commands\CategorizeThreads::class,
          Commands\AssignSentimentScore::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        //->saturdays()
        //0 8 * * 2,6
        //http://corntab.com/

        $file = storage_path('logs/cronlogs.txt');
       // $schedule->command('digest:weekly')
       // ->cron('22 8 * * 2,6')
        //->at('08:00')
       // ->timezone('Asia/Kolkata')
      // ->sendOutputTo($file)
       // ->emailOutputTo('reach-us@weopined.com');
        //->evenInMaintenanceMode();
		
		
		$schedule->command('LoadData:Daily')
        ->cron('20 1 * * *');
        
       // $schedule->command('notification:daily')
        //->cron('25 16 * * 1')
        //->at('08:00')
        //->timezone('Asia/Kolkata');
        // ->sendOutputTo($file)
        // ->emailOutputTo('goutam.aman@gmail.com');
        //->evenInMaintenanceMode();

        $schedule->command('email:send')->dailyAt('6:00');

       $schedule->command('fetch:news-headlines')->dailyAt('12:00');
      // $schedule->command('chatgpt:schedule')->everyMinute();
      //$schedule->command('threads:categorize')->dailyAt('01:00');
     
     // $schedule->command('sentiment:assign-score')->dailyAt('01:00');

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
