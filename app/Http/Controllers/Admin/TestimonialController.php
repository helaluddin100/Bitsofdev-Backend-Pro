<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class TestimonialController extends Controller
{
    /**
     * Display a listing of testimonials.
     */
    public function index(Request $request)
    {
        $query = Testimonial::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            switch ($request->status) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'featured':
                    $query->where('is_featured', true);
                    break;
                case 'verified':
                    $query->where('is_verified', true);
                    break;
            }
        }

        // Filter by project type
        if ($request->has('project_type') && $request->project_type) {
            $query->where('project_type', $request->project_type);
        }

        // Filter by rating
        if ($request->has('rating') && $request->rating) {
            $query->where('rating', $request->rating);
        }

        $testimonials = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get statistics
        $stats = [
            'total' => Testimonial::count(),
            'active' => Testimonial::where('is_active', true)->count(),
            'featured' => Testimonial::where('is_featured', true)->count(),
            'verified' => Testimonial::where('is_verified', true)->count(),
            'average_rating' => round(Testimonial::avg('rating'), 1),
            'pending' => Testimonial::where('is_active', false)->count()
        ];

        return view('admin.testimonials.index', compact('testimonials', 'stats'));
    }

    /**
     * Show the form for creating a new testimonial.
     */
    public function create()
    {
        return view('admin.testimonials.create');
    }

    /**
     * Store a newly created testimonial.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'content' => 'required|string|min:10',
            'rating' => 'required|integer|min:1|max:5',
            'project_type' => 'nullable|string|max:255',
            'project_name' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            Testimonial::create([
                'name' => $request->name,
                'role' => $request->role,
                'company' => $request->company,
                'email' => $request->email,
                'content' => $request->content,
                'rating' => $request->rating,
                'project_type' => $request->project_type,
                'project_name' => $request->project_name,
                'image' => $request->image,
                'location' => $request->location,
                'is_featured' => $request->boolean('is_featured'),
                'is_verified' => $request->boolean('is_verified'),
                'is_active' => $request->boolean('is_active'),
                'sort_order' => $request->sort_order ?? 0,
                'submitted_at' => now()
            ]);

            return redirect()->route('admin.testimonials.index')
                ->with('success', 'Testimonial created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating testimonial: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error creating testimonial.')
                ->withInput();
        }
    }

    /**
     * Display the specified testimonial.
     */
    public function show(Testimonial $testimonial)
    {
        return view('admin.testimonials.show', compact('testimonial'));
    }

    /**
     * Show the form for editing the specified testimonial.
     */
    public function edit(Testimonial $testimonial)
    {
        return view('admin.testimonials.edit', compact('testimonial'));
    }

    /**
     * Update the specified testimonial.
     */
    public function update(Request $request, Testimonial $testimonial)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'content' => 'required|string|min:10',
            'rating' => 'required|integer|min:1|max:5',
            'project_type' => 'nullable|string|max:255',
            'project_name' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $testimonial->update([
                'name' => $request->name,
                'role' => $request->role,
                'company' => $request->company,
                'email' => $request->email,
                'content' => $request->content,
                'rating' => $request->rating,
                'project_type' => $request->project_type,
                'project_name' => $request->project_name,
                'image' => $request->image,
                'location' => $request->location,
                'is_featured' => $request->boolean('is_featured'),
                'is_verified' => $request->boolean('is_verified'),
                'is_active' => $request->boolean('is_active'),
                'sort_order' => $request->sort_order ?? 0
            ]);

            return redirect()->route('admin.testimonials.index')
                ->with('success', 'Testimonial updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating testimonial: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error updating testimonial.')
                ->withInput();
        }
    }

    /**
     * Remove the specified testimonial.
     */
    public function destroy(Testimonial $testimonial)
    {
        try {
            $testimonial->delete();
            return redirect()->route('admin.testimonials.index')
                ->with('success', 'Testimonial deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting testimonial: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error deleting testimonial.');
        }
    }

    /**
     * Toggle testimonial status.
     */
    public function toggleStatus(Testimonial $testimonial)
    {
        try {
            $testimonial->update(['is_active' => !$testimonial->is_active]);
            $status = $testimonial->is_active ? 'activated' : 'deactivated';
            return response()->json([
                'success' => true,
                'message' => "Testimonial {$status} successfully.",
                'is_active' => $testimonial->is_active
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling testimonial status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating testimonial status.'
            ], 500);
        }
    }

    /**
     * Toggle featured status.
     */
    public function toggleFeatured(Testimonial $testimonial)
    {
        try {
            $testimonial->update(['is_featured' => !$testimonial->is_featured]);
            $status = $testimonial->is_featured ? 'featured' : 'unfeatured';
            return response()->json([
                'success' => true,
                'message' => "Testimonial {$status} successfully.",
                'is_featured' => $testimonial->is_featured
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling testimonial featured status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating testimonial featured status.'
            ], 500);
        }
    }

    /**
     * Bulk actions on testimonials.
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,feature,unfeature,delete',
            'testimonials' => 'required|array',
            'testimonials.*' => 'exists:testimonials,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request parameters.'
            ], 422);
        }

        try {
            $testimonials = Testimonial::whereIn('id', $request->testimonials);
            $count = $testimonials->count();

            switch ($request->action) {
                case 'activate':
                    $testimonials->update(['is_active' => true]);
                    $message = "{$count} testimonials activated successfully.";
                    break;
                case 'deactivate':
                    $testimonials->update(['is_active' => false]);
                    $message = "{$count} testimonials deactivated successfully.";
                    break;
                case 'feature':
                    $testimonials->update(['is_featured' => true]);
                    $message = "{$count} testimonials featured successfully.";
                    break;
                case 'unfeature':
                    $testimonials->update(['is_featured' => false]);
                    $message = "{$count} testimonials unfeatured successfully.";
                    break;
                case 'delete':
                    $testimonials->delete();
                    $message = "{$count} testimonials deleted successfully.";
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            Log::error('Error performing bulk action: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error performing bulk action.'
            ], 500);
        }
    }

    /**
     * Export testimonials to CSV.
     */
    public function export(Request $request)
    {
        try {
            $query = Testimonial::query();

            // Apply same filters as index
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('role', 'like', "%{$search}%")
                      ->orWhere('company', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
                });
            }

            $testimonials = $query->orderBy('created_at', 'desc')->get();

            $filename = 'testimonials_' . date('Y-m-d_H-i-s') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\""
            ];

            $callback = function() use ($testimonials) {
                $file = fopen('php://output', 'w');

                // CSV headers
                fputcsv($file, [
                    'ID', 'Name', 'Role', 'Company', 'Email', 'Content', 'Rating',
                    'Project Type', 'Project Name', 'Location', 'Featured', 'Verified',
                    'Active', 'Sort Order', 'Submitted At', 'Created At'
                ]);

                // CSV data
                foreach ($testimonials as $testimonial) {
                    fputcsv($file, [
                        $testimonial->id,
                        $testimonial->name,
                        $testimonial->role,
                        $testimonial->company,
                        $testimonial->email,
                        $testimonial->content,
                        $testimonial->rating,
                        $testimonial->project_type,
                        $testimonial->project_name,
                        $testimonial->location,
                        $testimonial->is_featured ? 'Yes' : 'No',
                        $testimonial->is_verified ? 'Yes' : 'No',
                        $testimonial->is_active ? 'Yes' : 'No',
                        $testimonial->sort_order,
                        $testimonial->submitted_at?->format('Y-m-d H:i:s'),
                        $testimonial->created_at->format('Y-m-d H:i:s')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Error exporting testimonials: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error exporting testimonials.');
        }
    }
}
