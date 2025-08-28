<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingPlan;
use App\Models\PricingFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PricingController extends Controller
{
    public function index()
    {
        $plans = PricingPlan::with('features')->ordered()->paginate(10);
        return view('admin.pricing.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.pricing.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'billing_cycle' => 'required|in:monthly,yearly,one-time',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'features.*.name' => 'required|string|max:255',
            'features.*.description' => 'nullable|string',
            'features.*.icon' => 'nullable|string|max:255',
            'features.*.is_available' => 'boolean'
        ]);

        $plan = PricingPlan::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'currency' => $request->currency,
            'billing_cycle' => $request->billing_cycle,
            'is_popular' => $request->has('is_popular'),
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0
        ]);

        if ($request->has('features')) {
            foreach ($request->features as $index => $feature) {
                $plan->features()->create([
                    'name' => $feature['name'],
                    'description' => $feature['description'] ?? null,
                    'icon' => $feature['icon'] ?? null,
                    'is_available' => $feature['is_available'] ?? true,
                    'sort_order' => $index
                ]);
            }
        }

        return redirect()->route('admin.pricing.index')->with('success', 'Pricing plan created successfully!');
    }

    public function edit(PricingPlan $plan)
    {
        $plan->load('features');
        return view('admin.pricing.edit', compact('plan'));
    }

    public function update(Request $request, PricingPlan $plan)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'billing_cycle' => 'required|in:monthly,yearly,one-time',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'features.*.name' => 'required|string|max:255',
            'features.*.description' => 'nullable|string',
            'features.*.icon' => 'nullable|string|max:255',
            'features.*.is_available' => 'boolean'
        ]);

        $plan->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'currency' => $request->currency,
            'billing_cycle' => $request->billing_cycle,
            'is_popular' => $request->has('is_popular'),
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0
        ]);

        // Update features
        if ($request->has('features')) {
            // Delete existing features
            $plan->features()->delete();

            // Create new features
            foreach ($request->features as $index => $feature) {
                $plan->features()->create([
                    'name' => $feature['name'],
                    'description' => $feature['description'] ?? null,
                    'icon' => $feature['icon'] ?? null,
                    'is_available' => $feature['is_available'] ?? true,
                    'sort_order' => $index
                ]);
            }
        }

        return redirect()->route('admin.pricing.index')->with('success', 'Pricing plan updated successfully!');
    }

    public function destroy(PricingPlan $plan)
    {
        $plan->features()->delete();
        $plan->delete();
        return redirect()->route('admin.pricing.index')->with('success', 'Pricing plan deleted successfully!');
    }
}
