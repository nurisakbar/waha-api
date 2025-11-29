<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\WhatsAppSession;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WhatsAppSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        $sessions = [];

        foreach ($users as $user) {
            // Create 1-3 sessions per user
            $sessionCount = rand(1, 3);

            for ($i = 0; $i < $sessionCount; $i++) {
                $statuses = ['pairing', 'connected', 'disconnected', 'failed'];
                $status = $statuses[array_rand($statuses)];

                $session = [
                    'id' => Str::uuid()->toString(),
                    'user_id' => $user->id,
                    'session_name' => $user->name . "'s Session " . ($i + 1),
                    'session_id' => 'session_' . $user->id . '_' . time() . '_' . uniqid(),
                    'status' => $status,
                    'waha_instance_url' => 'http://localhost:3000',
                    'created_at' => now()->subDays(rand(0, 30)),
                    'updated_at' => now(),
                ];

                // Add status-specific fields
                if ($status === 'connected') {
                    $session['connected_at'] = now()->subDays(rand(1, 20));
                    $session['last_activity_at'] = now()->subHours(rand(0, 24));
                } elseif ($status === 'pairing') {
                    $session['qr_code_expires_at'] = now()->addMinutes(2);
                } elseif ($status === 'disconnected') {
                    $session['disconnected_at'] = now()->subDays(rand(1, 10));
                }

                $sessions[] = $session;
            }
        }

        foreach ($sessions as $session) {
            WhatsAppSession::create($session);
        }

        $this->command->info('Created ' . count($sessions) . ' WhatsApp sessions.');
    }
}

