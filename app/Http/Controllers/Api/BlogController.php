<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Blog::with(['category', 'user'])
            ->published()
            ->latest('published_at');

        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Featured posts
        if ($request->has('featured') && $request->featured) {
            $query->featured();
        }

        $blogs = $query->paginate($request->get('per_page', 9));

        return response()->json([
            'success' => true,
            'data' => $blogs
        ]);
    }

    public function show($slug)
    {
        $blog = Blog::with(['category', 'user'])
            ->published()
            ->where('slug', $slug)
            ->first();

        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Blog post not found'
            ], 404);
        }

        // Increment view count
        $blog->increment('views');

        // Get related posts
        $relatedPosts = Blog::with(['category'])
            ->published()
            ->where('category_id', $blog->category_id)
            ->where('id', '!=', $blog->id)
            ->limit(3)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'blog' => $blog,
                'related_posts' => $relatedPosts
            ]
        ]);
    }

    public function categories()
    {
        $categories = Category::active()
            ->withCount(['blogs' => function ($query) {
                $query->published();
            }])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function featured()
    {
        $featuredPosts = Blog::with(['category', 'user'])
            ->published()
            ->featured()
            ->latest('published_at')
            ->limit(6)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $featuredPosts
        ]);
    }
}
