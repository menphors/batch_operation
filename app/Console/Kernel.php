<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use App\Http\Controllers\ChangeSubOfferingController;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;



class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
      commands\cron::class,
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
        $schedule->call(function () {
          $changeOfferingController =  new ChangeSubOfferingController();
          $changeOfferingController->scheduleChangePrimaryOffering();
        })->everyMinute(); 
        
        // $schedule->call(function () {
        //     $HotBillController =  new HotBillController();
        //     $HotBillController->getExchangeRate();
        //   })->everyMinute(); 

        $schedule->command('minute:update')
        ->cron('* * * * *');
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
