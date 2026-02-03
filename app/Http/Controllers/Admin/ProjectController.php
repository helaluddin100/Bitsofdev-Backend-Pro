<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::latest()->paginate(6);
        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('admin.projects.create');
    }

    public function store(Request $request)
    {
        // Debug: Log the request data
        Log::info('Project Store Request', [
            'all_data' => $request->all(),
            'files' => $request->hasFile('featured_image') ? 'Has file' : 'No file',
            'file_info' => $request->hasFile('featured_image') ? [
                'name' => $request->file('featured_image')->getClientOriginalName(),
                'size' => $request->file('featured_image')->getSize(),
                'mime' => $request->file('featured_image')->getMimeType()
            ] : null
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
            'project_url' => 'nullable|url|max:255',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'slug' => 'nullable|string|max:255|unique:projects,slug'
        ]);

        $data = $request->all();

        // Handle slug
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($request->title);
        }

        // Handle boolean fields
        $data['is_featured'] = $request->has('is_featured');
        $data['is_active'] = $request->has('is_active');

        // Handle featured image with WebP conversion
        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $this->processAndConvertImage($request->file('featured_image'));
        }

        Project::create($data);

        return redirect()->route('admin.projects.index')->with('success', 'Project created successfully!');
    }

    public function edit(Project $project)
    {
        return view('admin.projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
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
            'project_url' => 'nullable|url|max:255',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'slug' => 'nullable|string|max:255|unique:projects,slug,' . $project->id
        ]);

        $data = $request->all();

        // Handle slug
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($request->title);
        }

        // Handle boolean fields
        $data['is_featured'] = $request->has('is_featured');
        $data['is_active'] = $request->has('is_active');

        // Handle featured image with WebP conversion
        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($project->featured_image && file_exists(public_path($project->featured_image))) {
                unlink(public_path($project->featured_image));
            }

            $data['featured_image'] = $this->processAndConvertImage($request->file('featured_image'));
        }

        $project->update($data);

        return redirect()->route('admin.projects.index')->with('success', 'Project updated successfully!');
    }

    /**
     * Process and convert image to WebP format
     */
    private function processAndConvertImage($image)
    {
        $imageName = time() . '_' . Str::random(10);
        $webpPath = 'images/projects/' . $imageName . '.webp';

        // Create directory if it doesn't exist
        if (!file_exists(public_path('images/projects'))) {
            mkdir(public_path('images/projects'), 0755, true);
        }

        // Convert to WebP
        $sourceImage = imagecreatefromstring(file_get_contents($image->getRealPath()));

        if ($sourceImage) {
            // Get image dimensions
            $width = imagesx($sourceImage);
            $height = imagesy($sourceImage);

            // Create new image with max dimensions (1200x800 for projects)
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

    public function destroy(Project $project)
    {
        if ($project->featured_image && file_exists(public_path($project->featured_image))) {
            unlink(public_path($project->featured_image));
        }

        $project->delete();
        return redirect()->route('admin.projects.index')->with('success', 'Project deleted successfully!');
    }
}
