<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::with(['category', 'user'])->latest()->paginate(10);
        return view('admin.blogs.index', compact('blogs'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        Log::info('Blog Create Method', [
            'categories_count' => $categories->count(),
            'categories' => $categories->pluck('name', 'id')->toArray()
        ]);
        return view('admin.blogs.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // Log the request for debugging
        Log::info('Blog Store Request', [
            'method' => $request->method(),
            'all_data' => $request->all(),
            'files' => $request->hasFile('featured_image') ? 'Has file' : 'No file'
        ]);

        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,published,archived',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160'
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->title);
        $data['user_id'] = auth()->id();
        $data['published_at'] = $request->status === 'published' ? now() : null;
        $data['is_featured'] = $request->has('is_featured');

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $this->processAndConvertImage($request->file('featured_image'));
        }

        Blog::create($data);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post created successfully!');
    }

    public function edit(Blog $blog)
    {
        $categories = Category::active()->get();
        return view('admin.blogs.edit', compact('blog', 'categories'));
    }

    public function show(Blog $blog)
    {
        try {
            $blog->load(['category', 'user']);

            // Log the show request for debugging
            Log::info('Blog Show Request', [
                'blog_id' => $blog->id,
                'blog_title' => $blog->title,
                'blog_status' => $blog->status,
                'has_category' => $blog->category ? true : false,
                'has_user' => $blog->user ? true : false
            ]);

            return view('admin.blogs.show', compact('blog'));
        } catch (\Exception $e) {
            Log::error('Blog Show Error', [
                'blog_id' => $blog->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['error' => 'Failed to load blog post: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, Blog $blog)
    {
        try {
            // Log the update request for debugging
            Log::info('Blog Update Request', [
                'blog_id' => $blog->id,
                'method' => $request->method(),
                'all_data' => $request->all(),
                'files' => $request->hasFile('featured_image') ? 'Has file' : 'No file'
            ]);

            $request->validate([
                'title' => 'required|string|max:255',
                'excerpt' => 'required|string',
                'content' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'status' => 'required|in:draft,published,archived',
                'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'is_featured' => 'boolean',
                'meta_title' => 'nullable|string|max:60',
                'meta_description' => 'nullable|string|max:160'
            ]);

            $data = $request->all();
            $data['slug'] = Str::slug($request->title);
            $data['published_at'] = $request->status === 'published' ? now() : null;
            $data['is_featured'] = $request->has('is_featured');

            if ($request->hasFile('featured_image')) {
                // Delete old image
                if ($blog->featured_image && file_exists(public_path($blog->featured_image))) {
                    unlink(public_path($blog->featured_image));
                }

                $data['featured_image'] = $this->processAndConvertImage($request->file('featured_image'));
            }

            // Log the data before update
            Log::info('Blog Update Data', [
                'blog_id' => $blog->id,
                'update_data' => $data
            ]);

            $blog->update($data);

            // Log successful update
            Log::info('Blog Updated Successfully', [
                'blog_id' => $blog->id,
                'title' => $blog->title
            ]);

            return redirect()->route('admin.blogs.index')->with('success', 'Blog post updated successfully!');
        } catch (\Exception $e) {
            // Log the error
            Log::error('Blog Update Error', [
                'blog_id' => $blog->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()->withErrors(['error' => 'Failed to update blog post: ' . $e->getMessage()]);
        }
    }

    public function destroy(Blog $blog)
    {
        if ($blog->featured_image && file_exists(public_path($blog->featured_image))) {
            unlink(public_path($blog->featured_image));
        }

        $blog->delete();
        return redirect()->route('admin.blogs.index')->with('success', 'Blog post deleted successfully!');
    }

    /**
     * Process and convert image to WebP format
     */
    private function processAndConvertImage($image)
    {
        $imageName = time() . '_' . Str::random(10);
        $webpPath = 'images/blogs/' . $imageName . '.webp';

        // Create directory if it doesn't exist
        if (!file_exists(public_path('images/blogs'))) {
            mkdir(public_path('images/blogs'), 0755, true);
        }

        // Convert to WebP
        $sourceImage = imagecreatefromstring(file_get_contents($image->getRealPath()));

        if ($sourceImage) {
            // Get image dimensions
            $width = imagesx($sourceImage);
            $height = imagesy($sourceImage);

            // Create new image with max dimensions (1200x800 for blog posts)
            $maxWidth = 1200;
            $maxHeight = 800;

            if ($width > $maxWidth || $height > $maxHeight) {
                $ratio = min($maxWidth / $width, $maxHeight / $height);
                $newWidth = round($width * $ratio);
                $newHeight = round($height * $ratio);

                $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                $sourceImage = $resizedImage;
            }

            // Save as WebP
            imagewebp($sourceImage, public_path($webpPath), 85); // 85% quality
            imagedestroy($sourceImage);

            return $webpPath;
        }

        return null;
    }

    /**
     * Generate SEO suggestions for the blog post
     */
    public function getSeoSuggestions(Request $request)
    {
        // Log the entire request for debugging
        Log::info('SEO Analysis Request Received', [
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'body' => $request->getContent(),
            'json' => $request->json()->all(),
            'all' => $request->all()
        ]);

        // Get data from JSON request body
        $data = $request->json()->all();
        $title = $data['title'] ?? '';
        $content = $data['content'] ?? '';

        // Log for debugging
        Log::info('SEO Analysis Request Data', ['title' => $title, 'content' => $content]);

        $suggestions = [
            'meta_title' => $this->generateMetaTitle($title),
            'meta_description' => $this->generateMetaDescription($content),
            'seo_score' => $this->calculateSeoScore($title, $content),
            'recommendations' => $this->getSeoRecommendations($title, $content)
        ];

        // Log suggestions for debugging
        Log::info('SEO Suggestions Generated', $suggestions);

        return response()->json($suggestions);
    }

    /**
     * Test SEO method for debugging
     */
    public function testSeo()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'SEO test endpoint is working',
            'timestamp' => now()
        ]);
    }

    /**
     * Test method for debugging route model binding
     */
    public function testBinding($id)
    {
        try {
            $blog = Blog::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'blog' => [
                    'id' => $blog->id,
                    'title' => $blog->title,
                    'status' => $blog->status,
                    'category_id' => $blog->category_id,
                    'user_id' => $blog->user_id
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'id_requested' => $id
            ], 500);
        }
    }

    /**
     * Simple test method to check if controller is accessible
     */
    public function testController()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'BlogController is working correctly',
            'timestamp' => now(),
            'available_methods' => [
                'index',
                'create',
                'store',
                'show',
                'edit',
                'update',
                'destroy'
            ]
        ]);
    }

    /**
     * Debug method to list all blogs
     */
    public function debugBlogs()
    {
        try {
            $blogs = Blog::with(['category', 'user'])->get();

            return response()->json([
                'status' => 'success',
                'total_blogs' => $blogs->count(),
                'blogs' => $blogs->map(function ($blog) {
                    return [
                        'id' => $blog->id,
                        'title' => $blog->title,
                        'status' => $blog->status,
                        'category' => $blog->category ? $blog->category->name : 'No Category',
                        'user' => $blog->user ? $blog->user->name : 'No User'
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    private function generateMetaTitle($title)
    {
        $metaTitle = Str::limit($title, 60, '');
        return $metaTitle;
    }

    private function generateMetaDescription($content)
    {
        // Remove HTML tags and get clean text
        $cleanContent = strip_tags($content);
        $metaDescription = Str::limit($cleanContent, 160, '...');
        return $metaDescription;
    }

    private function calculateSeoScore($title, $content)
    {
        $score = 0;

        // Title length check (50-60 characters is optimal)
        $titleLength = strlen($title);
        if ($titleLength >= 50 && $titleLength <= 60) {
            $score += 25;
        } elseif ($titleLength >= 30 && $titleLength <= 70) {
            $score += 15;
        }

        // Content length check (minimum 300 words)
        $wordCount = str_word_count(strip_tags($content));
        if ($wordCount >= 300) {
            $score += 25;
        } elseif ($wordCount >= 200) {
            $score += 15;
        }

        // Check for keywords in title
        if (preg_match('/\b(how|what|why|when|where|best|top|guide|tutorial|tips)\b/i', $title)) {
            $score += 20;
        }

        // Check for numbers in title (attracts more clicks)
        if (preg_match('/\d+/', $title)) {
            $score += 15;
        }

        // Check for emotional words
        $emotionalWords = ['amazing', 'incredible', 'essential', 'ultimate', 'complete', 'comprehensive'];
        foreach ($emotionalWords as $word) {
            if (stripos($title, $word) !== false) {
                $score += 5;
                break;
            }
        }

        return min($score, 100);
    }

    private function getSeoRecommendations($title, $content)
    {
        $recommendations = [];

        // Title recommendations
        $titleLength = strlen($title);
        if ($titleLength < 30) {
            $recommendations[] = 'Title is too short. Aim for 50-60 characters.';
        } elseif ($titleLength > 70) {
            $recommendations[] = 'Title is too long. Keep it under 60 characters.';
        }

        if (!preg_match('/\d+/', $title)) {
            $recommendations[] = 'Consider adding numbers to your title for better CTR.';
        }

        // Content recommendations
        $wordCount = str_word_count(strip_tags($content));
        if ($wordCount < 300) {
            $recommendations[] = 'Content is too short. Aim for at least 300 words.';
        }

        if (!preg_match('/\b(how|what|why|when|where|best|top|guide|tutorial|tips)\b/i', $title)) {
            $recommendations[] = 'Consider using question words or action words in your title.';
        }

        return $recommendations;
    }
}
