<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    /**
     * Display the analytics dashboard
     */
    public function index(Request $request)
    {
        $days = $request->get('days', 30);

        // Get analytics data
        $data = [
            'total_visitors' => Visitor::getUniqueVisitorsCount(),
            'daily_visitors' => Visitor::getUniqueVisitorsCount($days),
            'returning_visitors' => Visitor::getReturningVisitorsCount($days),
            'avg_session_duration' => round(Visitor::getAverageSessionDuration($days) / 60, 2), // Convert to minutes
            'top_pages' => Visitor::getTopVisitedPages($days, 10),
            'most_clicked' => Visitor::getMostClickedActions($days, 10),
            'visitors_by_country' => Visitor::getVisitorsByCountry($days),
            'visitor_trend' => Visitor::getVisitorTrend($days),
            'recent_visitors' => Visitor::latest()->paginate(20),
        ];

        // Handle empty data for charts
        if ($data['visitor_trend']->isEmpty()) {
            $data['visitor_trend'] = collect([
                (object)['date' => now()->subDays($days - 1)->format('Y-m-d'), 'unique_visitors' => 0, 'total_visits' => 0],
                (object)['date' => now()->format('Y-m-d'), 'unique_visitors' => 0, 'total_visits' => 0]
            ]);
        }

        if ($data['visitors_by_country']->isEmpty()) {
            $data['visitors_by_country'] = collect([
                (object)['country' => 'No data yet', 'count' => 1]
            ]);
        }

        return view('admin.analytics.index', compact('data', 'days'));
    }

    /**
     * Get analytics data for AJAX requests
     */
    public function getData(Request $request)
    {
        $days = $request->get('days', 30);
        $type = $request->get('type', 'trend');

        switch ($type) {
            case 'trend':
                $data = Visitor::getVisitorTrend($days);
                break;
            case 'country':
                $data = Visitor::getVisitorsByCountry($days);
                break;
            case 'pages':
                $data = Visitor::getTopVisitedPages($days, 10);
                break;
            case 'actions':
                $data = Visitor::getMostClickedActions($days, 10);
                break;
            default:
                $data = [];
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Export visitor data
     */
    public function export(Request $request)
    {
        $days = $request->get('days', 30);
        $format = $request->get('format', 'csv');

        $visitors = Visitor::where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc')
            ->get();

        if ($format === 'csv') {
            return $this->exportToCsv($visitors);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unsupported export format'
        ], 400);
    }

    /**
     * Export data to CSV
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

            // Add headers
            fputcsv($file, [
                'ID',
                'Visitor ID',
                'IP',
                'Country',
                'City',
                'Device',
                'Browser',
                'OS',
                'Page URL',
                'Referrer',
                'Time Spent (seconds)',
                'Page Entered',
                'Page Exited',
                'Created At'
            ]);

            // Add data
            foreach ($visitors as $visitor) {
                fputcsv($file, [
                    $visitor->id,
                    $visitor->visitor_id,
                    $visitor->ip,
                    $visitor->location['country'] ?? 'Unknown',
                    $visitor->location['city'] ?? 'Unknown',
                    $visitor->device,
                    $visitor->browser,
                    $visitor->os,
                    $visitor->page_url,
                    $visitor->referrer,
                    $visitor->time_spent,
                    $visitor->page_entered_at,
                    $visitor->page_exited_at,
                    $visitor->created_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
