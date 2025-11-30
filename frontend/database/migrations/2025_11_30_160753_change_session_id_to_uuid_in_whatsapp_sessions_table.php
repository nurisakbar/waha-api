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
        if (Schema::hasTable('whatsapp_sessions')) {
            // Drop unique constraint if exists
            try {
                Schema::table('whatsapp_sessions', function (Blueprint $table) {
                    $table->dropUnique(['session_id']);
                });
            } catch (\Exception $e) {
                // Unique constraint might not exist
            }

            // Drop index if exists (but keep it if it's part of unique)
            try {
                Schema::table('whatsapp_sessions', function (Blueprint $table) {
                    $table->dropIndex(['session_id']);
                });
            } catch (\Exception $e) {
                // Index might not exist separately
            }

            // Update existing non-UUID session_ids to UUID format
            // This will generate new UUIDs for existing records
            $sessions = DB::table('whatsapp_sessions')->whereNotNull('session_id')->get();
            foreach ($sessions as $session) {
                // Check if session_id is already a valid UUID
                if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $session->session_id)) {
                    // Generate new UUID for non-UUID session_ids
                    DB::table('whatsapp_sessions')
                        ->where('id', $session->id)
                        ->update(['session_id' => (string) \Illuminate\Support\Str::uuid()]);
                }
            }

            // Change column type from string to uuid (CHAR(36) for MySQL)
            DB::statement('ALTER TABLE whatsapp_sessions MODIFY session_id CHAR(36) NOT NULL');

            // Add unique constraint back
            Schema::table('whatsapp_sessions', function (Blueprint $table) {
                $table->unique('session_id');
            });

            // Add index back
            Schema::table('whatsapp_sessions', function (Blueprint $table) {
                $table->index('session_id');
            });

            // Update comment
            DB::statement("ALTER TABLE whatsapp_sessions MODIFY session_id CHAR(36) NOT NULL COMMENT 'WAHA session ID (UUID)'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('whatsapp_sessions')) {
            // Drop unique constraint
            Schema::table('whatsapp_sessions', function (Blueprint $table) {
                $table->dropUnique(['session_id']);
            });

            // Drop index
            Schema::table('whatsapp_sessions', function (Blueprint $table) {
                $table->dropIndex(['session_id']);
            });

            // Change back to string
            Schema::table('whatsapp_sessions', function (Blueprint $table) {
                $table->string('session_id')->unique()->change()->comment('WAHA session ID');
            });

            // Add index back
            Schema::table('whatsapp_sessions', function (Blueprint $table) {
                $table->index('session_id');
            });
        }
    }
};
