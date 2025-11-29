<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Reset free text quota setiap tanggal 1 setiap bulan
Schedule::command('quota:reset-free-text')
    ->monthlyOn(1, '00:00')
    ->timezone('Asia/Jakarta')
    ->description('Reset free text quota untuk semua user setiap tanggal 1');
