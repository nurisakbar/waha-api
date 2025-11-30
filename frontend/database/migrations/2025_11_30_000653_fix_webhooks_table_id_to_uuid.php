<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('webhooks')) {
            return;
        }

        // Check if id is already UUID
        $column = DB::select("SHOW COLUMNS FROM webhooks WHERE Field = 'id'");
        if (!empty($column) && str_contains($column[0]->Type, 'char(36)')) {
            // Already UUID, skip
            return;
        }

        // Drop foreign keys that reference webhooks.id
        try {
            Schema::table('webhook_logs', function (Blueprint $table) {
                $table->dropForeign(['webhook_id']);
            });
        } catch (\Exception $e) {
            // Foreign key might not exist
        }

        // Drop primary key
        try {
            Schema::table('webhooks', function (Blueprint $table) {
                $table->dropPrimary();
            });
        } catch (\Exception $e) {
            // Primary key might not exist
        }

        // Convert existing IDs to UUIDs
        $webhooks = DB::table('webhooks')->get();
        $idMapping = [];
        
        foreach ($webhooks as $webhook) {
            $oldId = $webhook->id;
            $newId = (string) Str::uuid();
            $idMapping[$oldId] = $newId;
        }

        // Update webhook_logs first
        if (Schema::hasTable('webhook_logs')) {
            foreach ($idMapping as $oldId => $newId) {
                DB::table('webhook_logs')
                    ->where('webhook_id', $oldId)
                    ->update(['webhook_id' => $newId]);
            }
        }

        // Change webhooks.id column type to UUID
        DB::statement('ALTER TABLE webhooks MODIFY id CHAR(36) NOT NULL');

        // Update webhooks.id with new UUIDs
        foreach ($idMapping as $oldId => $newId) {
            DB::table('webhooks')
                ->where('id', $oldId)
                ->update(['id' => $newId]);
        }

        // Add primary key back (only if it doesn't exist)
        $primaryKeyExists = DB::select("SHOW KEYS FROM webhooks WHERE Key_name = 'PRIMARY'");
        if (empty($primaryKeyExists)) {
            DB::statement('ALTER TABLE webhooks ADD PRIMARY KEY (id)');
        }

        // Recreate foreign key for webhook_logs
        if (Schema::hasTable('webhook_logs')) {
            try {
                DB::statement('ALTER TABLE webhook_logs MODIFY webhook_id CHAR(36) NOT NULL');
                Schema::table('webhook_logs', function (Blueprint $table) {
                    $table->foreign('webhook_id')->references('id')->on('webhooks')->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Foreign key might already exist
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be safely reversed
        // as we don't have the original integer IDs
    }
};
