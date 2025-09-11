<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Response;
use App\Models\CampaignLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketingController extends Controller
{
    /**
     * Display the marketing dashboard
     */
    public function index()
    {
        // Get dashboard statistics
        $stats = $this->getDashboardStats();

        // Get recent campaigns
        $recentCampaigns = Campaign::with('leads')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent responses
        $recentResponses = Response::with(['lead', 'campaign'])
            ->orderBy('response_date', 'desc')
            ->limit(10)
            ->get();

        // Get category performance
        $categoryPerformance = $this->getCategoryPerformance();

        // Get monthly campaign performance
        $monthlyPerformance = $this->getMonthlyPerformance();

        return view('admin.marketing.dashboard', compact(
            'stats',
            'recentCampaigns',
            'recentResponses',
            'categoryPerformance',
            'monthlyPerformance'
        ));
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats()
    {
        return [
            'total_leads' => Lead::count(),
            'active_leads' => Lead::active()->count(),
            'total_campaigns' => Campaign::count(),
            'active_campaigns' => Campaign::active()->count(),
            'sent_emails' => CampaignLead::where('status', '!=', 'fresh')->count(),
            'pending_reminders' => $this->getPendingRemindersCount(),
            'total_responses' => Response::count(),
            'response_rate' => $this->getOverallResponseRate(),
            'qualified_leads' => Response::qualified()->count(),
            'this_month_leads' => Lead::whereMonth('created_at', now()->month)->count(),
            'this_month_campaigns' => Campaign::whereMonth('created_at', now()->month)->count(),
            'this_month_responses' => Response::whereMonth('response_date', now()->month)->count()
        ];
    }

    /**
     * Get pending reminders count
     */
    private function getPendingRemindersCount()
    {
        $reminder1Count = CampaignLead::readyForReminder1()->count();
        $reminder2Count = CampaignLead::readyForReminder2()->count();

        return $reminder1Count + $reminder2Count;
    }

    /**
     * Get overall response rate
     */
    private function getOverallResponseRate()
    {
        $totalSent = CampaignLead::where('status', '!=', 'fresh')->count();
        $responses = CampaignLead::where('status', 'responded')->count();

        return $totalSent > 0 ? round(($responses / $totalSent) * 100, 2) : 0;
    }

    /**
     * Get category performance
     */
    private function getCategoryPerformance()
    {
        return Category::active()
            ->withCount(['leads', 'campaigns'])
            ->get()
            ->map(function($category) {
                $totalLeads = $category->leads_count;

                // Count responded leads for this category
                $respondedLeads = CampaignLead::whereHas('lead', function($query) use ($category) {
                    $query->where('category', $category->name);
                })->where('status', 'responded')->count();

                return [
                    'name' => $category->name,
                    'total_leads' => $totalLeads,
                    'campaigns' => $category->campaigns_count,
                    'responded_leads' => $respondedLeads,
                    'response_rate' => $totalLeads > 0 ? round(($respondedLeads / $totalLeads) * 100, 2) : 0
                ];
            })
            ->sortByDesc('response_rate')
            ->take(10);
    }

    /**
     * Get monthly performance data
     */
    private function getMonthlyPerformance()
    {
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = [
                'month' => $date->format('M Y'),
                'leads' => Lead::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'campaigns' => Campaign::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'responses' => Response::whereYear('response_date', $date->year)
                    ->whereMonth('response_date', $date->month)
                    ->count()
            ];
        }

        return $months;
    }

    /**
     * Get leads analytics
     */
    public function leadsAnalytics()
    {
        $leadsByCategory = Lead::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->get();

        $leadsByMunicipality = Lead::select('municipality', DB::raw('count(*) as count'))
            ->whereNotNull('municipality')
            ->groupBy('municipality')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        $leadsByMonth = Lead::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', now()->subYear())
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        return response()->json([
            'leads_by_category' => $leadsByCategory,
            'leads_by_municipality' => $leadsByMunicipality,
            'leads_by_month' => $leadsByMonth
        ]);
    }

    /**
     * Get campaign analytics
     */
    public function campaignAnalytics()
    {
        $campaignsByStatus = Campaign::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $campaignsByCategory = Campaign::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->get();

        $responseRates = Campaign::select('name', 'response_rate')
            ->where('response_rate', '>', 0)
            ->orderBy('response_rate', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'campaigns_by_status' => $campaignsByStatus,
            'campaigns_by_category' => $campaignsByCategory,
            'response_rates' => $responseRates
        ]);
    }

    /**
     * Export leads data
     */
    public function exportLeads(Request $request)
    {
        $query = Lead::with('category');

        // Apply filters
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('municipality')) {
            $query->where('municipality', $request->municipality);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $leads = $query->get();

        // Generate CSV
        $filename = 'leads_export_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($leads) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'ID', 'Name', 'Email', 'Phone', 'Company', 'Category',
                'Address', 'Municipality', 'Website', 'Rating', 'Review Count',
                'Facebook', 'Instagram', 'Twitter', 'Yelp', 'Status', 'Created At'
            ]);

            // CSV data
            foreach ($leads as $lead) {
                fputcsv($file, [
                    $lead->id,
                    $lead->name,
                    $lead->email,
                    $lead->phone,
                    $lead->company,
                    $lead->category,
                    $lead->address,
                    $lead->municipality,
                    $lead->website,
                    $lead->rating,
                    $lead->review_count,
                    $lead->facebook,
                    $lead->instagram,
                    $lead->twitter,
                    $lead->yelp,
                    $lead->is_active ? 'Active' : 'Inactive',
                    $lead->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
