<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class TestimonialController extends Controller
{
    /**
     * Display a listing of testimonials.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Testimonial::active()->ordered();

            // Filter by featured
            if ($request->has('featured') && $request->boolean('featured')) {
                $query->featured();
            }

            // Filter by project type
            if ($request->has('project_type') && $request->project_type) {
                $query->byProjectType($request->project_type);
            }

            // Filter by rating
            if ($request->has('min_rating') && $request->min_rating) {
                $query->byRating($request->min_rating);
            }

            // Filter by verified
            if ($request->has('verified') && $request->boolean('verified')) {
                $query->verified();
            }

            // Search by name, role, company, or content
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('role', 'like', "%{$search}%")
                      ->orWhere('company', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
                });
            }

            // Pagination
            $perPage = $request->get('per_page', 12);
            $testimonials = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $testimonials->items(),
                'pagination' => [
                    'current_page' => $testimonials->currentPage(),
                    'last_page' => $testimonials->lastPage(),
                    'per_page' => $testimonials->perPage(),
                    'total' => $testimonials->total(),
                    'has_more' => $testimonials->hasMorePages()
                ],
                'meta' => [
                    'average_rating' => Testimonial::getAverageRating(),
                    'total_count' => Testimonial::getTotalCount(),
                    'rating_distribution' => Testimonial::getRatingDistribution(),
                    'project_type_distribution' => Testimonial::getProjectTypeDistribution()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching testimonials: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching testimonials'
            ], 500);
        }
    }

    /**
     * Display featured testimonials.
     */
    public function featured(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 6);
            $testimonials = Testimonial::active()
                ->featured()
                ->ordered()
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $testimonials->items(),
                'pagination' => [
                    'current_page' => $testimonials->currentPage(),
                    'last_page' => $testimonials->lastPage(),
                    'per_page' => $testimonials->perPage(),
                    'total' => $testimonials->total(),
                    'has_more' => $testimonials->hasMorePages()
                ],
                'meta' => [
                    'average_rating' => Testimonial::getAverageRating(),
                    'total_count' => Testimonial::getTotalCount(),
                    'rating_distribution' => Testimonial::getRatingDistribution(),
                    'project_type_distribution' => Testimonial::getProjectTypeDistribution()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching featured testimonials: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching featured testimonials'
            ], 500);
        }
    }

    /**
     * Display testimonials by project type.
     */
    public function byProjectType(string $projectType): JsonResponse
    {
        try {
            $testimonials = Testimonial::active()
                ->byProjectType($projectType)
                ->ordered()
                ->limit(8)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $testimonials,
                'project_type' => $projectType
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching testimonials by project type: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching testimonials by project type'
            ], 500);
        }
    }

    /**
     * Display testimonials statistics.
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'total_testimonials' => Testimonial::getTotalCount(),
                'featured_testimonials' => Testimonial::active()->featured()->count(),
                'average_rating' => round(Testimonial::getAverageRating(), 1),
                'rating_distribution' => Testimonial::getRatingDistribution(),
                'project_type_distribution' => Testimonial::getProjectTypeDistribution(),
                'recent_testimonials' => Testimonial::active()
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(['name', 'rating', 'project_type', 'created_at'])
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching testimonial statistics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching testimonial statistics'
            ], 500);
        }
    }

    /**
     * Store a newly created testimonial.
     */
    public function store(Request $request): JsonResponse
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
            'metadata' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $testimonial = Testimonial::create([
                ...$request->validated(),
                'submitted_at' => now(),
                'is_active' => false, // Require admin approval
                'is_verified' => false
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Testimonial submitted successfully. It will be reviewed before being published.',
                'data' => $testimonial
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating testimonial: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating testimonial'
            ], 500);
        }
    }

    /**
     * Display the specified testimonial.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $testimonial = Testimonial::active()->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $testimonial
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Testimonial not found'
            ], 404);
        }
    }

    /**
     * Get project types for filtering.
     */
    public function projectTypes(): JsonResponse
    {
        $projectTypes = [
            'web-development' => 'Web Development',
            'mobile-app' => 'Mobile App',
            'ui-ux-design' => 'UI/UX Design',
            'e-commerce' => 'E-commerce',
            'consulting' => 'Consulting',
            'seo' => 'SEO Services',
            'digital-marketing' => 'Digital Marketing',
            'other' => 'Other'
        ];

        return response()->json([
            'success' => true,
            'data' => $projectTypes
        ]);
    }

    /**
     * Update the specified testimonial.
     */
    public function update(Request $request, Testimonial $testimonial): JsonResponse
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
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $testimonial->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Testimonial updated successfully.',
                'data' => $testimonial
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating testimonial: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating testimonial'
            ], 500);
        }
    }

    /**
     * Remove the specified testimonial.
     */
    public function destroy(Testimonial $testimonial): JsonResponse
    {
        try {
            $testimonial->delete();

            return response()->json([
                'success' => true,
                'message' => 'Testimonial deleted successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting testimonial: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting testimonial'
            ], 500);
        }
    }

    /**
     * Toggle testimonial status.
     */
    public function toggleStatus(Testimonial $testimonial): JsonResponse
    {
        try {
            $testimonial->update(['is_active' => !$testimonial->is_active]);
            $status = $testimonial->is_active ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "Testimonial {$status} successfully.",
                'data' => $testimonial
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
    public function toggleFeatured(Testimonial $testimonial): JsonResponse
    {
        try {
            $testimonial->update(['is_featured' => !$testimonial->is_featured]);
            $status = $testimonial->is_featured ? 'featured' : 'unfeatured';

            return response()->json([
                'success' => true,
                'message' => "Testimonial {$status} successfully.",
                'data' => $testimonial
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
    public function bulkAction(Request $request): JsonResponse
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
            return response()->json([
                'success' => false,
                'message' => 'Error exporting testimonials.'
            ], 500);
        }
    }
}
