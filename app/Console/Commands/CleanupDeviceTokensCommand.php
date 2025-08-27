<?php

namespace App\Console\Commands;

use App\Services\FCM\DeviceTokenService;
use Illuminate\Console\Command;

class CleanupDeviceTokensCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'fcm:cleanup-tokens 
                            {--dry-run : Show what would be cleaned without actually doing it}
                            {--days=90 : Number of days to consider tokens as stale}';

    /**
     * The console command description.
     */
    protected $description = 'Clean up old and inactive FCM device tokens';

    /**
     * Execute the console command.
     */
    public function handle(DeviceTokenService $deviceTokenService): int
    {
        $dryRun = $this->option('dry-run');
        $days = (int) $this->option('days');

        $this->info("Cleaning up device tokens older than {$days} days...");

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
            
            $count = \DB::table('personal_access_tokens')
                ->where('last_used_at', '<', now()->subDays($days))
                ->whereNotNull('device_token')
                ->count();
                
            $this->info("Would clean up {$count} stale device tokens");
        } else {
            $count = $deviceTokenService->removeStaleTokens();
            $this->info("Cleaned up {$count} stale device tokens");
        }

        return Command::SUCCESS;
    }
}