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
        // Modify the message_type enum to include 'poll'
        // MySQL doesn't support ALTER ENUM directly, so we use raw SQL
        DB::statement("ALTER TABLE `messages` MODIFY COLUMN `message_type` ENUM('text', 'image', 'video', 'audio', 'document', 'location', 'contact', 'sticker', 'voice', 'poll') NOT NULL DEFAULT 'text'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'poll' from the enum
        DB::statement("ALTER TABLE `messages` MODIFY COLUMN `message_type` ENUM('text', 'image', 'video', 'audio', 'document', 'location', 'contact', 'sticker', 'voice') NOT NULL DEFAULT 'text'");
    }
};
