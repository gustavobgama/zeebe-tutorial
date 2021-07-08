<?php

namespace App\Console;

use App\Console\Commands\CreateOrder;
use App\Console\Commands\DeployProcess;
use App\Console\Commands\OrderPayment;
use App\Console\Commands\OrderShipment;
use App\Console\Commands\OrderPaymentReceived;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        DeployProcess::class,
        CreateOrder::class,
        OrderPayment::class,
        OrderPaymentReceived::class,
        OrderShipment::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
