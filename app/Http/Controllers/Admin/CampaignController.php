<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Lead;
use App\Models\Category;
use App\Models\CampaignLead;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CampaignController extends Controller
{
    /**
     * Display a listing of campaigns
     */
    public function index(Request $request)
    {
        $query = Campaign::withCount(['leads', 'responses']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email_subject', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $campaigns = $query->paginate(20);
        $categories = Category::active()->get();

        return view('admin.marketing.campaigns.index', compact(
            'campaigns',
            'categories'
        ));
    }

    /**
     * Show the form for creating a new campaign
     */
    public function create()
    {
        $categories = Category::active()->get();
        return view('admin.marketing.campaigns.create', compact('categories'));
    }

    /**
     * Store a newly created campaign
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'email_subject' => 'required|string|max:255',
            'email_body' => 'required|string',
            'schedule_type' => 'required|in:immediate,scheduled',
            'scheduled_at' => 'nullable|date|after:now',
            'enable_reminders' => 'boolean',
            'reminder_1_days' => 'nullable|integer|min:1|max:30',
            'reminder_2_days' => 'nullable|integer|min:1|max:30',
            'reminder_1_subject' => 'nullable|string|max:255',
            'reminder_1_body' => 'nullable|string',
            'reminder_2_subject' => 'nullable|string|max:255',
            'reminder_2_body' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $campaign = Campaign::create($request->all());

        return redirect()->route('admin.marketing.campaigns.show', $campaign)
            ->with('success', 'Campaign created successfully.');
    }

    /**
     * Display the specified campaign
     */
    public function show(Campaign $campaign)
    {
        $campaign->load(['leads', 'responses.lead']);

        // Get campaign statistics
        $stats = $campaign->stats;

        // Get leads by status
        $leadsByStatus = [
            'fresh' => $campaign->getFreshLeads(),
            'sent' => $campaign->getSentLeads(),
            'reminder_1' => $campaign->getLeadsByStatus('reminder_1'),
            'reminder_2' => $campaign->getLeadsByStatus('reminder_2'),
            'responded' => $campaign->getRespondedLeads()
        ];

        // Get recent responses
        $recentResponses = $campaign->responses()
            ->with('lead')
            ->orderBy('response_date', 'desc')
            ->limit(10)
            ->get();

        return view('admin.marketing.campaigns.show', compact(
            'campaign',
            'stats',
            'leadsByStatus',
            'recentResponses'
        ));
    }

    /**
     * Show the form for editing the campaign
     */
    public function edit(Campaign $campaign)
    {
        $categories = Category::active()->get();
        return view('admin.marketing.campaigns.edit', compact('campaign', 'categories'));
    }

    /**
     * Update the specified campaign
     */
    public function update(Request $request, Campaign $campaign)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'email_subject' => 'required|string|max:255',
            'email_body' => 'required|string',
            'schedule_type' => 'required|in:immediate,scheduled',
            'scheduled_at' => 'nullable|date|after:now',
            'enable_reminders' => 'boolean',
            'reminder_1_days' => 'nullable|integer|min:1|max:30',
            'reminder_2_days' => 'nullable|integer|min:1|max:30',
            'reminder_1_subject' => 'nullable|string|max:255',
            'reminder_1_body' => 'nullable|string',
            'reminder_2_subject' => 'nullable|string|max:255',
            'reminder_2_body' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $campaign->update($request->all());

        return redirect()->route('admin.marketing.campaigns.show', $campaign)
            ->with('success', 'Campaign updated successfully.');
    }

    /**
     * Remove the specified campaign
     */
    public function destroy(Campaign $campaign)
    {
        $campaign->delete();

        return redirect()->route('admin.marketing.campaigns.index')
            ->with('success', 'Campaign deleted successfully.');
    }

    /**
     * Add leads to campaign
     */
    public function addLeads(Request $request, Campaign $campaign)
    {
        $validator = Validator::make($request->all(), [
            'lead_ids' => 'required|array',
            'lead_ids.*' => 'exists:leads,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Invalid lead selection.');
        }

        $leads = Lead::whereIn('id', $request->lead_ids)->get();
        $addedCount = 0;

        foreach ($leads as $lead) {
            // Check if lead is already in campaign
            if (!$campaign->leads()->where('lead_id', $lead->id)->exists()) {
                $campaign->leads()->attach($lead->id, [
                    'status' => 'fresh',
                    'email_address' => $lead->email
                ]);
                $addedCount++;
            }
        }

        // Update campaign stats
        $campaign->updateStats();

        return redirect()->back()
            ->with('success', "{$addedCount} leads added to campaign successfully.");
    }

    /**
     * Remove leads from campaign
     */
    public function removeLeads(Request $request, Campaign $campaign)
    {
        $validator = Validator::make($request->all(), [
            'lead_ids' => 'required|array',
            'lead_ids.*' => 'exists:leads,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Invalid lead selection.');
        }

        $campaign->leads()->detach($request->lead_ids);

        // Update campaign stats
        $campaign->updateStats();

        return redirect()->back()
            ->with('success', 'Leads removed from campaign successfully.');
    }

    /**
     * Send campaign
     */
    public function send(Campaign $campaign)
    {
        if (!$campaign->canBeSent()) {
            return redirect()->back()
                ->with('error', 'Campaign cannot be sent at this time.');
        }

        // Dispatch job to send campaign
        \App\Jobs\SendCampaignJob::dispatch($campaign);

        $campaign->update(['status' => 'sending']);

        return redirect()->back()
            ->with('success', 'Campaign is being sent. You will be notified when complete.');
    }

    /**
     * Pause campaign
     */
    public function pause(Campaign $campaign)
    {
        $campaign->update(['status' => 'paused']);

        return redirect()->back()
            ->with('success', 'Campaign paused successfully.');
    }

    /**
     * Resume campaign
     */
    public function resume(Campaign $campaign)
    {
        $campaign->update(['status' => 'scheduled']);

        return redirect()->back()
            ->with('success', 'Campaign resumed successfully.');
    }

    /**
     * Duplicate campaign
     */
    public function duplicate(Campaign $campaign)
    {
        $newCampaign = $campaign->replicate();
        $newCampaign->name = $campaign->name . ' (Copy)';
        $newCampaign->status = 'draft';
        $newCampaign->scheduled_at = null;
        $newCampaign->sent_at = null;
        $newCampaign->save();

        return redirect()->route('admin.marketing.campaigns.edit', $newCampaign)
            ->with('success', 'Campaign duplicated successfully.');
    }

    /**
     * Get campaign performance data
     */
    public function performance(Campaign $campaign)
    {
        $performance = [
            'total_leads' => $campaign->total_leads,
            'sent_count' => $campaign->sent_count,
            'response_count' => $campaign->response_count,
            'response_rate' => $campaign->response_rate,
            'leads_by_status' => CampaignLead::where('campaign_id', $campaign->id)
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get(),
            'daily_sends' => CampaignLead::where('campaign_id', $campaign->id)
                ->whereNotNull('sent_at')
                ->select(DB::raw('DATE(sent_at) as date'), DB::raw('count(*) as count'))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'daily_responses' => Response::where('campaign_id', $campaign->id)
                ->select(DB::raw('DATE(response_date) as date'), DB::raw('count(*) as count'))
                ->groupBy('date')
                ->orderBy('date')
                ->get()
        ];

        return response()->json($performance);
    }

    /**
     * Export campaign data
     */
    public function export(Campaign $campaign)
    {
        $campaignLeads = $campaign->campaignLeads()
            ->with('lead')
            ->get();

        $filename = 'campaign_' . $campaign->id . '_export_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($campaignLeads) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Lead ID', 'Lead Name', 'Email', 'Phone', 'Status',
                'Sent At', 'Reminder 1 Sent At', 'Reminder 2 Sent At',
                'Responded At', 'Response Message'
            ]);

            // CSV data
            foreach ($campaignLeads as $campaignLead) {
                fputcsv($file, [
                    $campaignLead->lead->id,
                    $campaignLead->lead->name,
                    $campaignLead->lead->email,
                    $campaignLead->lead->phone,
                    $campaignLead->status,
                    $campaignLead->sent_at ? $campaignLead->sent_at->format('Y-m-d H:i:s') : '',
                    $campaignLead->reminder_1_sent_at ? $campaignLead->reminder_1_sent_at->format('Y-m-d H:i:s') : '',
                    $campaignLead->reminder_2_sent_at ? $campaignLead->reminder_2_sent_at->format('Y-m-d H:i:s') : '',
                    $campaignLead->responded_at ? $campaignLead->responded_at->format('Y-m-d H:i:s') : '',
                    $campaignLead->response_message
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
