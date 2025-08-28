<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        return view('admin.blogs.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,published,archived',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_featured' => 'boolean'
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->title);
        $data['user_id'] = auth()->id();
        $data['published_at'] = $request->status === 'published' ? now() : null;

        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/blogs'), $imageName);
            $data['featured_image'] = 'images/blogs/' . $imageName;
        }

        Blog::create($data);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post created successfully!');
    }

    public function edit(Blog $blog)
    {
        $categories = Category::active()->get();
        return view('admin.blogs.edit', compact('blog', 'categories'));
    }

    public function update(Request $request, Blog $blog)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,published,archived',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_featured' => 'boolean'
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->title);
        $data['published_at'] = $request->status === 'published' ? now() : null;

        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($blog->featured_image && file_exists(public_path($blog->featured_image))) {
                unlink(public_path($blog->featured_image));
            }

            $image = $request->file('featured_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/blogs'), $imageName);
            $data['featured_image'] = 'images/blogs/' . $imageName;
        }

        $blog->update($data);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post updated successfully!');
    }

    public function destroy(Blog $blog)
    {
        if ($blog->featured_image && file_exists(public_path($blog->featured_image))) {
            unlink(public_path($blog->featured_image));
        }

        $blog->delete();
        return redirect()->route('admin.blogs.index')->with('success', 'Blog post deleted successfully!');
    }
}
