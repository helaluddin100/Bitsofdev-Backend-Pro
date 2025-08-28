<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::active();

        // Filter by status
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        // Filter by featured
        if ($request->has('featured') && $request->featured) {
            $query->featured();
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Filter by technologies
        if ($request->has('technologies')) {
            $technologies = explode(',', $request->technologies);
            $query->where(function ($q) use ($technologies) {
                foreach ($technologies as $tech) {
                    $q->whereJsonContains('technologies', trim($tech));
                }
            });
        }

        $projects = $query->orderBy('priority', 'desc')
            ->latest()
            ->paginate($request->get('per_page', 9));

        return response()->json([
            'success' => true,
            'data' => $projects
        ]);
    }

    public function show($slug)
    {
        $project = Project::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        }

        // Get related projects
        $relatedProjects = Project::active()
            ->where('id', '!=', $project->id)
            ->where('status', 'completed')
            ->limit(3)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'project' => $project,
                'related_projects' => $relatedProjects
            ]
        ]);
    }

    public function featured()
    {
        $featuredProjects = Project::active()
            ->featured()
            ->orderBy('priority', 'desc')
            ->limit(6)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $featuredProjects
        ]);
    }

    public function byStatus($status)
    {
        $projects = Project::active()
            ->byStatus($status)
            ->orderBy('priority', 'desc')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $projects
        ]);
    }

    public function technologies()
    {
        $technologies = Project::active()
            ->whereNotNull('technologies')
            ->pluck('technologies')
            ->flatten()
            ->unique()
            ->values();

        return response()->json([
            'success' => true,
            'data' => $technologies
        ]);
    }
}
