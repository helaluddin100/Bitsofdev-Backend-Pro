<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Response;
use App\Models\Lead;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ResponseController extends Controller
{
    /**
     * Display a listing of responses
     */
    public function index(Request $request)
    {
        $query = Response::with(['lead', 'campaign']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('response_message', 'like', "%{$search}%")
                  ->orWhereHas('lead', function($leadQuery) use ($search) {
                      $leadQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('response_type')) {
            $query->where('response_type', $request->response_type);
        }

        if ($request->filled('sentiment')) {
            $query->where('sentiment', $request->sentiment);
        }

        if ($request->filled('is_qualified')) {
            $query->where('is_qualified', $request->is_qualified === 'true');
        }

        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->campaign_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('response_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('response_date', '<=', $request->date_to);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'response_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $responses = $query->paginate(20);
        $campaigns = Campaign::select('id', 'name')->get();

        return view('admin.marketing.responses.index', compact('responses', 'campaigns'));
    }

    /**
     * Display the specified response
     */
    public function show(Response $response)
    {
        $response->load(['lead', 'campaign', 'campaignLead']);

        // Get related responses from the same lead
        $relatedResponses = Response::where('lead_id', $response->lead_id)
            ->where('id', '!=', $response->id)
            ->with('campaign')
            ->orderBy('response_date', 'desc')
            ->limit(5)
            ->get();

        return view('admin.marketing.responses.show', compact('response', 'relatedResponses'));
    }

    /**
     * Show the form for editing the response
     */
    public function edit(Response $response)
    {
        $campaigns = Campaign::select('id', 'name')->get();
        return view('admin.marketing.responses.edit', compact('response', 'campaigns'));
    }

    /**
     * Update the specified response
     */
    public function update(Request $request, Response $response)
    {
        $validator = Validator::make($request->all(), [
            'response_message' => 'required|string',
            'response_type' => 'required|string|max:255',
            'sentiment' => 'nullable|in:positive,neutral,negative',
            'is_qualified' => 'boolean',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $response->update($request->all());

        return redirect()->route('admin.marketing.responses.show', $response)
            ->with('success', 'Response updated successfully.');
    }

    /**
     * Remove the specified response
     */
    public function destroy(Response $response)
    {
        $response->delete();

        return redirect()->route('admin.marketing.responses.index')
            ->with('success', 'Response deleted successfully.');
    }

    /**
     * Mark response as qualified
     */
    public function markQualified(Response $response)
    {
        $response->markAsQualified();

        return redirect()->back()
            ->with('success', 'Response marked as qualified.');
    }

    /**
     * Mark response as unqualified
     */
    public function markUnqualified(Response $response)
    {
        $response->markAsUnqualified();

        return redirect()->back()
            ->with('success', 'Response marked as unqualified.');
    }

    /**
     * Bulk update responses
     */
    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'response_ids' => 'required|array',
            'response_ids.*' => 'exists:responses,id',
            'action' => 'required|in:mark_qualified,mark_unqualified,delete'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Invalid selection or action.');
        }

        $responses = Response::whereIn('id', $request->response_ids);

        switch ($request->action) {
            case 'mark_qualified':
                $responses->update(['is_qualified' => true]);
                $message = 'Selected responses marked as qualified.';
                break;
            case 'mark_unqualified':
                $responses->update(['is_qualified' => false]);
                $message = 'Selected responses marked as unqualified.';
                break;
            case 'delete':
                $responses->delete();
                $message = 'Selected responses deleted.';
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Get response statistics
     */
    public function statistics()
    {
        $stats = [
            'total_responses' => Response::count(),
            'qualified_responses' => Response::qualified()->count(),
            'responses_by_type' => Response::select('response_type', DB::raw('count(*) as count'))
                ->groupBy('response_type')
                ->orderBy('count', 'desc')
                ->get(),
            'responses_by_sentiment' => Response::select('sentiment', DB::raw('count(*) as count'))
                ->whereNotNull('sentiment')
                ->groupBy('sentiment')
                ->orderBy('count', 'desc')
                ->get(),
            'responses_by_campaign' => Response::with('campaign')
                ->select('campaign_id', DB::raw('count(*) as count'))
                ->groupBy('campaign_id')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'monthly_responses' => Response::select(
                    DB::raw('YEAR(response_date) as year'),
                    DB::raw('MONTH(response_date) as month'),
                    DB::raw('count(*) as count')
                )
                ->where('response_date', '>=', now()->subYear())
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get()
        ];

        return response()->json($stats);
    }

    /**
     * Export responses data
     */
    public function export(Request $request)
    {
        $query = Response::with(['lead', 'campaign']);

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('response_message', 'like', "%{$search}%")
                  ->orWhereHas('lead', function($leadQuery) use ($search) {
                      $leadQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('response_type')) {
            $query->where('response_type', $request->response_type);
        }

        if ($request->filled('sentiment')) {
            $query->where('sentiment', $request->sentiment);
        }

        if ($request->filled('is_qualified')) {
            $query->where('is_qualified', $request->is_qualified === 'true');
        }

        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->campaign_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('response_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('response_date', '<=', $request->date_to);
        }

        $responses = $query->orderBy('response_date', 'desc')->get();

        $filename = 'responses_export_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($responses) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'ID', 'Lead Name', 'Lead Email', 'Campaign', 'Response Date',
                'Response Type', 'Sentiment', 'Qualified', 'Response Message', 'Notes'
            ]);

            // CSV data
            foreach ($responses as $response) {
                fputcsv($file, [
                    $response->id,
                    $response->lead->name,
                    $response->lead->email,
                    $response->campaign->name,
                    $response->response_date->format('Y-m-d H:i:s'),
                    $response->response_type,
                    $response->sentiment,
                    $response->is_qualified ? 'Yes' : 'No',
                    $response->response_message,
                    $response->notes
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
