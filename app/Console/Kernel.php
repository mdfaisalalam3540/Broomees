<?php
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\RecalculateReputation::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Daily reputation recalculation
        $schedule->command('reputation:recalculate --all')
            ->daily()
            ->at('02:00')
            ->onOneServer();

        // Cleanup expired tokens daily
        $schedule->call(function () {
            \App\Models\ApiToken::where('expires_at', '<', now())->delete();
        })->daily();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}