<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        Log::info('Product Store Request', [
            'all_data' => $request->all(),
            'files' => $request->hasFile('featured_image') ? 'Has file' : 'No file',
        ]);

        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string',
            'content' => 'required|string',
            'client' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:planning,in-progress,review,completed,on-hold',
            'priority' => 'required|in:low,medium,high,urgent',
            'technologies' => 'nullable|string|max:500',
            'product_url' => 'nullable|url|max:255',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'slug' => 'nullable|string|max:255|unique:products,slug'
        ]);

        $data = $request->all();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($request->title);
        }

        $data['is_featured'] = $request->has('is_featured');
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $this->processAndConvertImage($request->file('featured_image'));
        }

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }

    public function show(Product $product)
    {
        return redirect()->route('admin.products.edit', $product);
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string',
            'content' => 'required|string',
            'client' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:planning,in-progress,review,completed,on-hold',
            'priority' => 'required|in:low,medium,high,urgent',
            'technologies' => 'nullable|string|max:500',
            'product_url' => 'nullable|url|max:255',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id
        ]);

        $data = $request->all();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($request->title);
        }

        $data['is_featured'] = $request->has('is_featured');
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('featured_image')) {
            if ($product->featured_image && file_exists(public_path($product->featured_image))) {
                unlink(public_path($product->featured_image));
            }
            $data['featured_image'] = $this->processAndConvertImage($request->file('featured_image'));
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }

    private function processAndConvertImage($image)
    {
        $imageName = time() . '_' . Str::random(10);
        $webpPath = 'images/products/' . $imageName . '.webp';

        if (!file_exists(public_path('images/products'))) {
            mkdir(public_path('images/products'), 0755, true);
        }

        $sourceImage = imagecreatefromstring(file_get_contents($image->getRealPath()));

        if ($sourceImage) {
            $width = imagesx($sourceImage);
            $height = imagesy($sourceImage);
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

            imagewebp($sourceImage, public_path($webpPath), 85);
            imagedestroy($sourceImage);
            return $webpPath;
        }

        return null;
    }

    public function destroy(Product $product)
    {
        if ($product->featured_image && file_exists(public_path($product->featured_image))) {
            unlink(public_path($product->featured_image));
        }
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully!');
    }
}
