<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisitorDataController extends Controller
{
    /**
     * Display advanced visitor data with filters
     */
    public function index(Request $request)
    {
        $query = Visitor::query();

        // Apply filters
        $query = $this->applyFilters($query, $request);

        // Get paginated results
        $visitors = $query->latest()->paginate(50);

        // Get filter options for dropdowns
        $filterOptions = $this->getFilterOptions();

        // Get summary statistics
        $summary = $this->getSummaryStatistics($request);

        return view('admin.visitors.index', compact('visitors', 'filterOptions', 'summary'));
    }

    /**
     * Apply filters to the query
     */
    private function applyFilters($query, Request $request)
    {
        // Date range filter
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from . ' 00:00:00');
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        // Device filter
        if ($request->filled('device')) {
            $query->where('device', $request->device);
        }

        // Browser filter
        if ($request->filled('browser')) {
            $query->where('browser', 'like', '%' . $request->browser . '%');
        }

        // OS filter
        if ($request->filled('os')) {
            $query->where('os', 'like', '%' . $request->os . '%');
        }

        // Country filter
        if ($request->filled('country')) {
            $query->whereRaw("JSON_EXTRACT(location, '$.country') = ?", [$request->country]);
        }

        // City filter
        if ($request->filled('city')) {
            $query->whereRaw("JSON_EXTRACT(location, '$.city') = ?", [$request->city]);
        }

        // Page URL filter
        if ($request->filled('page_url')) {
            $query->where('page_url', 'like', '%' . $request->page_url . '%');
        }

        // Time spent filter
        if ($request->filled('time_spent_min')) {
            $query->where('time_spent', '>=', $request->time_spent_min);
        }
        if ($request->filled('time_spent_max')) {
            $query->where('time_spent', '<=', $request->time_spent_max);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('visitor_id', 'like', '%' . $search . '%')
                    ->orWhere('ip', 'like', '%' . $search . '%')
                    ->orWhere('page_url', 'like', '%' . $search . '%')
                    ->orWhere('browser', 'like', '%' . $search . '%')
                    ->orWhere('os', 'like', '%' . $search . '%');
            });
        }

        return $query;
    }

    /**
     * Get filter options for dropdowns
     */
    private function getFilterOptions()
    {
        return [
            'devices' => Visitor::distinct()->pluck('device')->filter()->values(),
            'browsers' => Visitor::distinct()->pluck('browser')->filter()->values(),
            'operating_systems' => Visitor::distinct()->pluck('os')->filter()->values(),
            'countries' => $this->getDistinctCountries(),
            'cities' => $this->getDistinctCities(),
        ];
    }

    /**
     * Get distinct countries from location data
     */
    private function getDistinctCountries()
    {
        return Visitor::whereNotNull('location')
            ->get()
            ->pluck('location')
            ->filter(function ($location) {
                return is_array($location) && isset($location['country']);
            })
            ->pluck('country')
            ->unique()
            ->values();
    }

    /**
     * Get distinct cities from location data
     */
    private function getDistinctCities()
    {
        return Visitor::whereNotNull('location')
            ->get()
            ->pluck('location')
            ->filter(function ($location) {
                return is_array($location) && isset($location['city']);
            })
            ->pluck('city')
            ->unique()
            ->values();
    }

    /**
     * Get summary statistics
     */
    private function getSummaryStatistics(Request $request)
    {
        $query = Visitor::query();
        $query = $this->applyFilters($query, $request);

        return [
            'total_visitors' => $query->count(),
            'unique_visitors' => $query->distinct('visitor_id')->count('visitor_id'),
            'avg_time_spent' => round($query->avg('time_spent') / 60, 2), // Convert to minutes
            'total_pages' => $query->distinct('page_url')->count('page_url'),
        ];
    }

    /**
     * Export visitor data
     */
    public function export(Request $request)
    {
        $query = Visitor::query();
        $query = $this->applyFilters($query, $request);

        $visitors = $query->get();

        if ($request->format === 'csv') {
            return $this->exportToCsv($visitors);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unsupported export format'
        ], 400);
    }

    /**
     * Export to CSV
     */
    private function exportToCsv($visitors)
    {
        $filename = 'visitors_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($visitors) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'ID',
                'Visitor ID',
                'IP',
                'Country',
                'City',
                'Region',
                'ISP',
                'Device',
                'Browser',
                'OS',
                'Page URL',
                'Referrer',
                'Time Spent (sec)',
                'Session ID',
                'Page Entered',
                'Page Exited',
                'Created At'
            ]);

            // CSV data
            foreach ($visitors as $visitor) {
                $location = $visitor->location;
                fputcsv($file, [
                    $visitor->id,
                    $visitor->visitor_id,
                    $visitor->ip,
                    $location['country'] ?? 'Unknown',
                    $location['city'] ?? 'Unknown',
                    $location['region'] ?? 'Unknown',
                    $visitor->isp ?? 'Unknown',
                    $visitor->device ?? 'Unknown',
                    $visitor->browser ?? 'Unknown',
                    $visitor->os ?? 'Unknown',
                    $visitor->page_url,
                    $visitor->referrer ?? 'Direct',
                    $visitor->time_spent ?? 0,
                    $visitor->session_id ?? 'N/A',
                    $visitor->page_entered_at?->format('Y-m-d H:i:s') ?? 'N/A',
                    $visitor->page_exited_at?->format('Y-m-d H:i:s') ?? 'N/A',
                    $visitor->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get visitor details
     */
    public function show(Visitor $visitor)
    {
        return view('admin.visitors.show', compact('visitor'));
    }

    /**
     * Delete visitor record
     */
    public function destroy(Visitor $visitor)
    {
        $visitor->delete();

        return redirect()->route('admin.visitors.index')
            ->with('success', 'Visitor record deleted successfully');
    }

    /**
     * Bulk delete visitors
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'visitor_ids' => 'required|array',
            'visitor_ids.*' => 'exists:visitors,id'
        ]);

        try {
            $deleted = Visitor::whereIn('id', $request->visitor_ids)->delete();

            return redirect()->route('admin.visitors.index')
                ->with('success', "{$deleted} visitor records deleted successfully");
        } catch (\Exception $e) {
            return redirect()->route('admin.visitors.index')
                ->with('error', 'Failed to delete visitor records: ' . $e->getMessage());
        }
    }
}
