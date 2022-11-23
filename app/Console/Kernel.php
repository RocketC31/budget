<?php

namespace App\Console;

use App\Helper;
use App\Jobs\FetchConversionRates;
use App\Jobs\GlobalBalanceRefresh;
use App\Jobs\ProcessRecurrings;
use App\Jobs\ResetDatabaseDemoMode;
use App\Jobs\SendWeeklyReports;
use App\Jobs\SyncStripeSubscriptions;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Redis;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new SyncStripeSubscriptions())->everyMinute()->when(function () {
            return Helper::arePlansEnabled() && !is_null(config('stripe.secret'));
        });

        // Daily
        $schedule->job(new ProcessRecurrings())->daily();
        $schedule->job(new FetchConversionRates())->daily();
        $schedule->job(new GlobalBalanceRefresh())->when(function () {
            if (config("app.redis_available", true)) {
                try {
                    Redis::connection();
                } catch (\Exception $e) {
                    return false;
                }
                return true;
            }
            return false;
        })->daily();
        $schedule->job(new ResetDatabaseDemoMode())->everyMinute()->when(function () {
            return config("app.demo_mode");
        });

        $schedule->job(new SendWeeklyReports())->weekly()->fridays()->at('21:00');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
