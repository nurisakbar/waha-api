<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update enum to include 'free_text_quota'
        DB::statement("ALTER TABLE quota_usage_logs MODIFY COLUMN quota_type ENUM('text_quota', 'multimedia_quota', 'balance', 'free_text_quota') NOT NULL COMMENT 'Jenis quota yang digunakan'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum (without free_text_quota)
        DB::statement("ALTER TABLE quota_usage_logs MODIFY COLUMN quota_type ENUM('text_quota', 'multimedia_quota', 'balance') NOT NULL COMMENT 'Jenis quota yang digunakan'");
    }
};
