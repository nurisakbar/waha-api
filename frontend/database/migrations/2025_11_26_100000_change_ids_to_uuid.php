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
        // Check if tables already use UUID (for fresh migrations)
        // If tables are created with UUID from the start, this migration will be a no-op
        
        // Check if users.id is already UUID
        $usersColumn = DB::select("SHOW COLUMNS FROM users WHERE Field = 'id'");
        if (!empty($usersColumn) && $usersColumn[0]->Type === 'char(36)') {
            // Already UUID, skip
            return;
        }

        // Tables that reference user_id
        $userForeignKeyTables = [
            'whatsapp_sessions',
            'messages',
            'webhooks',
            'api_keys',
            'subscriptions',
            'invoices',
            'usage_statistics',
            'api_usage_logs',
        ];

        // Tables that reference session_id
        $sessionForeignKeyTables = [
            'messages',
            'webhooks',
        ];

        // Drop all foreign keys first
        foreach ($userForeignKeyTables as $table) {
            if (Schema::hasTable($table)) {
                try {
                    Schema::table($table, function (Blueprint $table) {
                        $table->dropForeign(['user_id']);
                    });
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
            }
        }

        foreach ($sessionForeignKeyTables as $table) {
            if (Schema::hasTable($table)) {
                try {
                    Schema::table($table, function (Blueprint $table) {
                        $table->dropForeign(['session_id']);
                    });
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
            }
        }

        // Change users.id to UUID
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropPrimary();
            });
            DB::statement('ALTER TABLE users MODIFY id CHAR(36) NOT NULL');
            DB::statement('ALTER TABLE users ADD PRIMARY KEY (id)');
        }

        // Change whatsapp_sessions.id to UUID
        if (Schema::hasTable('whatsapp_sessions')) {
            Schema::table('whatsapp_sessions', function (Blueprint $table) {
                $table->dropPrimary();
            });
            DB::statement('ALTER TABLE whatsapp_sessions MODIFY id CHAR(36) NOT NULL');
            DB::statement('ALTER TABLE whatsapp_sessions ADD PRIMARY KEY (id)');
            DB::statement('ALTER TABLE whatsapp_sessions MODIFY user_id CHAR(36) NOT NULL');
        }

        // Change messages.id to UUID
        if (Schema::hasTable('messages')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->dropPrimary();
            });
            DB::statement('ALTER TABLE messages MODIFY id CHAR(36) NOT NULL');
            DB::statement('ALTER TABLE messages ADD PRIMARY KEY (id)');
            DB::statement('ALTER TABLE messages MODIFY user_id CHAR(36) NOT NULL');
            DB::statement('ALTER TABLE messages MODIFY session_id CHAR(36) NOT NULL');
        }

        // Change webhooks.id to UUID
        if (Schema::hasTable('webhooks')) {
            try {
                Schema::table('webhooks', function (Blueprint $table) {
                    $table->dropPrimary();
                });
                DB::statement('ALTER TABLE webhooks MODIFY id CHAR(36) NOT NULL');
                DB::statement('ALTER TABLE webhooks ADD PRIMARY KEY (id)');
            } catch (\Exception $e) {
                // Primary key might not exist or already changed
            }
        }

        // Change all other user_id foreign keys to UUID
        foreach ($userForeignKeyTables as $table) {
            if ($table !== 'whatsapp_sessions' && $table !== 'messages' && Schema::hasTable($table)) {
                try {
                    DB::statement("ALTER TABLE {$table} MODIFY user_id CHAR(36) NOT NULL");
                } catch (\Exception $e) {
                    // Column might not exist or already changed
                }
            }
        }

        // Change session_id foreign keys to UUID
        foreach ($sessionForeignKeyTables as $table) {
            if ($table !== 'messages' && Schema::hasTable($table)) {
                try {
                    DB::statement("ALTER TABLE {$table} MODIFY session_id CHAR(36) NULL");
                } catch (\Exception $e) {
                    // Column might not exist
                }
            }
        }

        // Recreate foreign keys
        if (Schema::hasTable('whatsapp_sessions')) {
            Schema::table('whatsapp_sessions', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('messages')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('session_id')->references('id')->on('whatsapp_sessions')->onDelete('cascade');
            });
        }

        // Recreate other foreign keys
        if (Schema::hasTable('webhooks')) {
            Schema::table('webhooks', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                if (Schema::hasColumn('webhooks', 'session_id')) {
                    $table->foreign('session_id')->references('id')->on('whatsapp_sessions')->onDelete('cascade');
                }
            });
        }

        // Fix webhook_logs.webhook_id to UUID
        if (Schema::hasTable('webhook_logs')) {
            try {
                Schema::table('webhook_logs', function (Blueprint $table) {
                    $table->dropForeign(['webhook_id']);
                });
                DB::statement('ALTER TABLE webhook_logs MODIFY webhook_id CHAR(36) NOT NULL');
                Schema::table('webhook_logs', function (Blueprint $table) {
                    $table->foreign('webhook_id')->references('id')->on('webhooks')->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Foreign key might not exist or already changed
            }
        }

        if (Schema::hasTable('api_keys')) {
            Schema::table('api_keys', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('usage_statistics')) {
            Schema::table('usage_statistics', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('api_usage_logs')) {
            Schema::table('api_usage_logs', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['session_id']);
        });

        Schema::table('whatsapp_sessions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // Revert to bigint
        Schema::table('users', function (Blueprint $table) {
            $table->dropPrimary();
        });

        DB::statement('ALTER TABLE users MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        DB::statement('ALTER TABLE users ADD PRIMARY KEY (id)');

        Schema::table('whatsapp_sessions', function (Blueprint $table) {
            $table->dropPrimary();
        });

        DB::statement('ALTER TABLE whatsapp_sessions MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        DB::statement('ALTER TABLE whatsapp_sessions ADD PRIMARY KEY (id)');
        DB::statement('ALTER TABLE whatsapp_sessions MODIFY user_id BIGINT UNSIGNED NOT NULL');

        Schema::table('messages', function (Blueprint $table) {
            $table->dropPrimary();
        });

        DB::statement('ALTER TABLE messages MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        DB::statement('ALTER TABLE messages ADD PRIMARY KEY (id)');
        DB::statement('ALTER TABLE messages MODIFY user_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE messages MODIFY session_id BIGINT UNSIGNED NOT NULL');

        // Recreate foreign keys
        Schema::table('whatsapp_sessions', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('whatsapp_sessions')->onDelete('cascade');
        });
    }
};

