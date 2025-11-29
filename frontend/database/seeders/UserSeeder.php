<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'phone' => '+6281234567890',
                'role' => 'admin',
                'subscription_status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password'),
                'phone' => '+6281234567891',
                'role' => 'user',
                'subscription_status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password'),
                'phone' => '+6281234567892',
                'role' => 'user',
                'subscription_status' => 'trial',
                'trial_ends_at' => now()->addDays(14),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Bob Wilson',
                'email' => 'bob@example.com',
                'password' => Hash::make('password'),
                'phone' => '+6281234567893',
                'role' => 'user',
                'subscription_status' => 'active',
                'email_verified_at' => now(),
                'last_login_at' => now()->subHours(2),
                'created_at' => now()->subDays(30),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Alice Brown',
                'email' => 'alice@example.com',
                'password' => Hash::make('password'),
                'phone' => '+6281234567894',
                'role' => 'user',
                'subscription_status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now()->subDays(15),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        $this->command->info('Created ' . count($users) . ' users.');
    }
}

