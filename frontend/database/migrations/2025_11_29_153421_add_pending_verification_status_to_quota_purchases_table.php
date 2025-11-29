<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify ENUM to include 'pending_verification'
        DB::statement("ALTER TABLE quota_purchases MODIFY COLUMN status ENUM('pending', 'pending_verification', 'completed', 'failed', 'cancelled') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original ENUM values
        DB::statement("ALTER TABLE quota_purchases MODIFY COLUMN status ENUM('pending', 'completed', 'failed', 'cancelled') DEFAULT 'pending'");
    }
};
