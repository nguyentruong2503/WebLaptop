<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Promotion;

class DeactivateExpiredPromotions extends Command
{
    protected $signature = 'promotions:deactivate';
    protected $description = 'Tự động tắt khuyến mãi đã hết hạn';

    public function handle()
    {
        $count = Promotion::where('is_active', true)
            ->where('end_date', '<', now())
            ->update(['is_active' => false]);

        $this->info("Đã tắt {$count} khuyến mãi hết hạn.");
    }
}
