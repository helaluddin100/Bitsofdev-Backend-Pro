<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PricingPlan;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function index()
    {
        $plans = PricingPlan::active()
            ->with(['features' => function ($query) {
                $query->available()->ordered();
            }])
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $plans
        ]);
    }

    public function show($slug)
    {
        $plan = PricingPlan::active()
            ->with(['features' => function ($query) {
                $query->available()->ordered();
            }])
            ->where('slug', $slug)
            ->first();

        if (!$plan) {
            return response()->json([
                'success' => false,
                'message' => 'Pricing plan not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $plan
        ]);
    }

    public function popular()
    {
        $popularPlans = PricingPlan::active()
            ->popular()
            ->with(['features' => function ($query) {
                $query->available()->ordered();
            }])
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $popularPlans
        ]);
    }

    public function byCycle($cycle)
    {
        $plans = PricingPlan::active()
            ->where('billing_cycle', $cycle)
            ->with(['features' => function ($query) {
                $query->available()->ordered();
            }])
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $plans
        ]);
    }
}
