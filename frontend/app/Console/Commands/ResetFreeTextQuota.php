<?php

namespace App\Console\Commands;

use App\Models\UserQuota;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResetFreeTextQuota extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quota:reset-free-text';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset free text quota untuk semua user setiap tanggal 1 (100 quota gratis dengan watermark)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai reset free text quota untuk semua user...');
        
        $resetCount = UserQuota::query()
            ->update(['free_text_quota' => 100]);
        
        Log::info('Free text quota reset completed', [
            'users_updated' => $resetCount,
            'quota_amount' => 100,
            'reset_date' => now()->toDateString(),
        ]);
        
        $this->info("Berhasil reset free text quota untuk {$resetCount} user (100 quota per user)");
        
        return Command::SUCCESS;
    }
}
