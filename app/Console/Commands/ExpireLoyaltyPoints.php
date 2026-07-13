<?php

namespace App\Console\Commands;

use App\Services\Customer\LoyaltyPointsService;
use Illuminate\Console\Command;

class ExpireLoyaltyPoints extends Command
{
    protected $signature = 'loyalty:expire-points';

    protected $description = 'Expire loyalty points that have passed their expiry date';

    public function handle(LoyaltyPointsService $loyaltyPointsService): int
    {
        $this->info('Memproses poin yang kadaluarsa...');

        $loyaltyPointsService->expirePoints();

        $this->info('Poin kadaluarsa berhasil diproses.');

        return Command::SUCCESS;
    }
}
