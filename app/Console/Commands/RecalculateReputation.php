<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ReputationService;

class RecalculateReputation extends Command
{
    protected $signature = 'reputation:recalculate
                            {--user= : Recalculate for specific user ID}
                            {--all : Recalculate for all users}';

    protected $description = 'Recalculate reputation scores for users';

    public function handle(ReputationService $reputationService)
    {
        if ($this->option('user')) {
            $userId = $this->option('user');
            $user = \App\Models\User::find($userId);

            if (!$user) {
                $this->error("User not found: {$userId}");
                return 1;
            }

            $score = $reputationService->calculateReputation($user);
            $this->info("Recalculated reputation for user {$user->username}: {$score}");

        } elseif ($this->option('all')) {
            $this->info('Starting reputation recalculation for all users...');

            $count = \App\Models\User::count();
            $bar = $this->output->createProgressBar($count);

            \App\Models\User::chunk(100, function ($users) use ($reputationService, $bar) {
                foreach ($users as $user) {
                    $reputationService->calculateReputation($user);
                    $bar->advance();
                }
            });

            $bar->finish();
            $this->newLine();
            $this->info('Reputation recalculation completed.');

        } else {
            $this->error('Please specify either --user=ID or --all');
            return 1;
        }

        return 0;
    }
}