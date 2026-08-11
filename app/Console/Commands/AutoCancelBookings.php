<?php

namespace App\Console\Commands;

use App\Services\BookingService;
use Illuminate\Console\Command;

class AutoCancelBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-cancel-bookings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis membatalkan booking pending yang terlambat lebih dari 30 menit (No-Show)';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $count = BookingService::autoCancelExpiredBookings();
        $this->info("Berhasil membatalkan {$count} booking yang melewati toleransi 30 menit.");
    }
}
