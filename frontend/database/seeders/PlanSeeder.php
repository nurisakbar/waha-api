<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Perfect for testing and small projects',
                'price' => 0.00,
                'currency' => 'USD',
                'billing_period' => 'monthly',
                'sessions_limit' => 1,
                'messages_per_month' => 100,
                'api_rate_limit' => 10,
                'webhook_limit' => 1,
                'features' => ['Basic messaging', '1 session', '100 messages/month', 'Basic support'],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'For small businesses',
                'price' => 9.99,
                'currency' => 'USD',
                'billing_period' => 'monthly',
                'sessions_limit' => 3,
                'messages_per_month' => 1000,
                'api_rate_limit' => 50,
                'webhook_limit' => 3,
                'features' => ['All Free features', '3 sessions', '1,000 messages/month', 'Email support'],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'For growing businesses',
                'price' => 29.99,
                'currency' => 'USD',
                'billing_period' => 'monthly',
                'sessions_limit' => 10,
                'messages_per_month' => 10000,
                'api_rate_limit' => 200,
                'webhook_limit' => 10,
                'features' => ['All Basic features', '10 sessions', '10,000 messages/month', 'Priority support', 'Advanced analytics'],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'For large organizations',
                'price' => 99.99,
                'currency' => 'USD',
                'billing_period' => 'monthly',
                'sessions_limit' => 50,
                'messages_per_month' => null, // unlimited
                'api_rate_limit' => 1000,
                'webhook_limit' => 50,
                'features' => ['All Pro features', '50 sessions', 'Unlimited messages', 'Dedicated support', 'Custom integrations', 'SLA guarantee'],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 4,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
