<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PricingPlan;
use App\Models\PricingFeature;

class PricingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter',
                'description' => 'Perfect for small businesses and startups',
                'price' => 29.99,
                'currency' => 'USD',
                'billing_cycle' => 'monthly',
                'is_popular' => false,
                'sort_order' => 1,
                'features' => [
                    'Up to 5 team members',
                    'Basic project management',
                    'Email support',
                    '5GB storage',
                    'Basic analytics'
                ]
            ],
            [
                'name' => 'Professional',
                'description' => 'Ideal for growing teams and businesses',
                'price' => 79.99,
                'currency' => 'USD',
                'billing_cycle' => 'monthly',
                'is_popular' => true,
                'sort_order' => 2,
                'features' => [
                    'Up to 25 team members',
                    'Advanced project management',
                    'Priority email support',
                    '25GB storage',
                    'Advanced analytics',
                    'Custom integrations',
                    'Team collaboration tools'
                ]
            ],
            [
                'name' => 'Enterprise',
                'description' => 'For large organizations with advanced needs',
                'price' => 199.99,
                'currency' => 'USD',
                'billing_cycle' => 'monthly',
                'is_popular' => false,
                'sort_order' => 3,
                'features' => [
                    'Unlimited team members',
                    'Enterprise project management',
                    '24/7 phone support',
                    'Unlimited storage',
                    'Advanced analytics & reporting',
                    'Custom integrations',
                    'Advanced security features',
                    'Dedicated account manager',
                    'Custom training sessions'
                ]
            ]
        ];

        foreach ($plans as $planData) {
            $features = $planData['features'];
            unset($planData['features']);

            $planData['slug'] = \Illuminate\Support\Str::slug($planData['name']);
            $plan = PricingPlan::create($planData);

            foreach ($features as $index => $feature) {
                $plan->features()->create([
                    'name' => $feature,
                    'is_available' => true,
                    'sort_order' => $index
                ]);
            }
        }
    }
}
