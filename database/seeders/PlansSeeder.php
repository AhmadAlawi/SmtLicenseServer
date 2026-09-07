<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

/** Default plan tiers — codes are what a POS instance's plan_code field will hold. Adjust stripe_price_id once the Stripe products exist. */
class PlansSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'code' => 'starter',
                'name' => 'Starter',
                'seat_limit' => 3,
                'features' => ['multi_store' => false, 'advanced_reporting' => false],
            ],
            [
                'code' => 'pro',
                'name' => 'Pro',
                'seat_limit' => 10,
                'features' => ['multi_store' => true, 'advanced_reporting' => true],
            ],
            [
                'code' => 'business',
                'name' => 'Business',
                'seat_limit' => null,
                'features' => ['multi_store' => true, 'advanced_reporting' => true],
            ],
        ];

        foreach ($plans as $plan) {
            Plan::query()->updateOrCreate(['code' => $plan['code']], $plan);
        }
    }
}
