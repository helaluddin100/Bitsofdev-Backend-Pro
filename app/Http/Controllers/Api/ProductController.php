<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active();

        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        if ($request->has('featured') && $request->featured) {
            $query->featured();
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->has('technologies')) {
            $technologies = explode(',', $request->technologies);
            $query->where(function ($q) use ($technologies) {
                foreach ($technologies as $tech) {
                    $q->orWhere('technologies', 'like', '%' . trim($tech) . '%');
                }
            });
        }

        $products = $query->orderBy('priority', 'desc')
            ->latest()
            ->paginate($request->get('per_page', 9));

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $relatedProducts = Product::active()
            ->where('id', '!=', $product->id)
            ->where('status', 'completed')
            ->limit(3)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'product' => $product,
                'related_products' => $relatedProducts
            ]
        ]);
    }

    public function featured()
    {
        $featuredProducts = Product::active()
            ->featured()
            ->orderBy('priority', 'desc')
            ->limit(6)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $featuredProducts
        ]);
    }

    public function byStatus($status)
    {
        $products = Product::active()
            ->byStatus($status)
            ->orderBy('priority', 'desc')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function technologies()
    {
        $technologies = Product::active()
            ->whereNotNull('technologies')
            ->where('technologies', '!=', '')
            ->pluck('technologies')
            ->map(function ($tech) {
                return array_map('trim', explode(',', $tech));
            })
            ->flatten()
            ->unique()
            ->values();

        return response()->json([
            'success' => true,
            'data' => $technologies
        ]);
    }
}
