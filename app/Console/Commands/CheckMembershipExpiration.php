<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Membership;
use Illuminate\Support\Facades\Log;

class CheckMembershipExpiration extends Command
{
    protected $signature = 'membership:check-expiration';
    protected $description = 'Check and expire memberships that have reached their end date';

    public function handle()
    {
        $expiredCount = Membership::where('status', 'active')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);

        if ($expiredCount > 0) {
            $this->info("Expired {$expiredCount} memberships.");
            Log::info("Membership expiration check: Expired {$expiredCount} memberships.");
        } else {
            $this->info('No memberships to expire.');
        }

        return Command::SUCCESS;
    }
}
