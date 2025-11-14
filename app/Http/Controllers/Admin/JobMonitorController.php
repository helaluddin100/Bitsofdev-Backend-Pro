<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

class JobMonitorController extends Controller
{
    /**
     * Display jobs monitoring page
     */
    public function index()
    {
        // Get jobs from database queue
        $jobs = DB::table('jobs')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        // Get failed jobs
        $failedJobs = DB::table('failed_jobs')
            ->orderBy('failed_at', 'desc')
            ->limit(50)
            ->get();

        // Get queue stats
        $stats = [
            'pending' => DB::table('jobs')->count(),
            'failed' => DB::table('failed_jobs')->count(),
            'processing' => DB::table('jobs')->whereNotNull('reserved_at')->count(),
            'processed_today' => DB::table('jobs')
                ->whereDate('created_at', today())
                ->whereNotNull('reserved_at')
                ->count(),
            'failed_today' => DB::table('failed_jobs')
                ->whereDate('failed_at', today())
                ->count(),
        ];

        // Get email statistics from campaign_leads
        $emailStats = [
            'total_sent' => DB::table('campaign_leads')->whereNotNull('sent_at')->count(),
            'total_failed' => DB::table('campaign_leads')
                ->whereNotNull('metadata')
                ->whereRaw("JSON_EXTRACT(metadata, '$.failed_permanently') = true")
                ->count(),
            'sent_today' => DB::table('campaign_leads')
                ->whereDate('sent_at', today())
                ->count(),
            'failed_today' => DB::table('campaign_leads')
                ->whereDate('created_at', today())
                ->whereNotNull('metadata')
                ->whereRaw("JSON_EXTRACT(metadata, '$.failed_permanently') = true")
                ->count(),
        ];

        // Get failed emails from campaign_leads
        $failedEmails = DB::table('campaign_leads')
            ->join('campaigns', 'campaign_leads.campaign_id', '=', 'campaigns.id')
            ->join('leads', 'campaign_leads.lead_id', '=', 'leads.id')
            ->whereNotNull('campaign_leads.metadata')
            ->whereRaw("JSON_EXTRACT(campaign_leads.metadata, '$.failed_permanently') = true")
            ->select(
                'campaign_leads.id',
                'campaign_leads.email_address',
                'campaign_leads.metadata',
                'campaign_leads.created_at',
                'campaigns.name as campaign_name',
                'leads.name as lead_name',
                'leads.email as lead_email'
            )
            ->orderBy('campaign_leads.created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($item) {
                $metadata = json_decode($item->metadata, true);
                return [
                    'id' => $item->id,
                    'campaign_name' => $item->campaign_name,
                    'lead_name' => $item->lead_name,
                    'email_address' => $item->email_address ?: $item->lead_email,
                    'failed_at' => $metadata['failed_at'] ?? $item->created_at,
                    'failure_reason' => $metadata['failure_reason'] ?? $metadata['last_error'] ?? 'Unknown error'
                ];
            });

        return view('admin.marketing.jobs.index', compact('jobs', 'failedJobs', 'stats', 'emailStats', 'failedEmails'));
    }

    /**
     * Get jobs data via AJAX
     */
    public function getJobs(Request $request)
    {
        $jobs = DB::table('jobs')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($job) {
                $payload = json_decode($job->payload, true);
                return [
                    'id' => $job->id,
                    'queue' => $job->queue,
                    'attempts' => $job->attempts,
                    'reserved_at' => $job->reserved_at ? date('Y-m-d H:i:s', $job->reserved_at) : null,
                    'created_at' => date('Y-m-d H:i:s', $job->created_at),
                    'available_at' => date('Y-m-d H:i:s', $job->available_at),
                    'job_class' => $payload['displayName'] ?? 'Unknown',
                    'status' => $job->reserved_at ? 'processing' : 'pending'
                ];
            });

        $failedJobs = DB::table('failed_jobs')
            ->orderBy('failed_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($job) {
                $payload = json_decode($job->payload, true);
                return [
                    'id' => $job->id,
                    'uuid' => $job->uuid,
                    'connection' => $job->connection,
                    'queue' => $job->queue,
                    'failed_at' => $job->failed_at,
                    'exception' => substr($job->exception, 0, 200),
                    'job_class' => $payload['displayName'] ?? 'Unknown'
                ];
            });

        $stats = [
            'pending' => DB::table('jobs')->count(),
            'failed' => DB::table('failed_jobs')->count(),
            'processing' => DB::table('jobs')->whereNotNull('reserved_at')->count(),
        ];

        // Get email statistics
        $emailStats = [
            'total_sent' => DB::table('campaign_leads')->whereNotNull('sent_at')->count(),
            'total_failed' => DB::table('campaign_leads')
                ->whereNotNull('metadata')
                ->whereRaw("JSON_EXTRACT(metadata, '$.failed_permanently') = true")
                ->count(),
            'sent_today' => DB::table('campaign_leads')
                ->whereDate('sent_at', today())
                ->count(),
            'failed_today' => DB::table('campaign_leads')
                ->whereDate('created_at', today())
                ->whereNotNull('metadata')
                ->whereRaw("JSON_EXTRACT(metadata, '$.failed_permanently') = true")
                ->count(),
        ];

        // Get failed emails from campaign_leads
        $failedEmails = DB::table('campaign_leads')
            ->join('campaigns', 'campaign_leads.campaign_id', '=', 'campaigns.id')
            ->join('leads', 'campaign_leads.lead_id', '=', 'leads.id')
            ->whereNotNull('campaign_leads.metadata')
            ->whereRaw("JSON_EXTRACT(campaign_leads.metadata, '$.failed_permanently') = true")
            ->select(
                'campaign_leads.id',
                'campaign_leads.email_address',
                'campaign_leads.metadata',
                'campaign_leads.created_at',
                'campaigns.name as campaign_name',
                'leads.name as lead_name',
                'leads.email as lead_email'
            )
            ->orderBy('campaign_leads.created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($item) {
                $metadata = json_decode($item->metadata, true);
                return [
                    'id' => $item->id,
                    'campaign_name' => $item->campaign_name,
                    'lead_name' => $item->lead_name,
                    'email_address' => $item->email_address ?: $item->lead_email,
                    'failed_at' => $metadata['failed_at'] ?? $item->created_at,
                    'failure_reason' => $metadata['failure_reason'] ?? $metadata['last_error'] ?? 'Unknown error'
                ];
            });

        return response()->json([
            'jobs' => $jobs,
            'failedJobs' => $failedJobs,
            'stats' => $stats,
            'emailStats' => $emailStats,
            'failedEmails' => $failedEmails
        ]);
    }

    /**
     * Retry failed job
     */
    public function retry($id)
    {
        $failedJob = DB::table('failed_jobs')->where('id', $id)->first();
        
        if (!$failedJob) {
            return redirect()->back()->with('error', 'Failed job not found.');
        }

        // Move back to jobs table
        DB::table('jobs')->insert([
            'queue' => $failedJob->queue,
            'payload' => $failedJob->payload,
            'attempts' => 0,
            'reserved_at' => null,
            'available_at' => now()->timestamp,
            'created_at' => now()->timestamp
        ]);

        // Delete from failed_jobs
        DB::table('failed_jobs')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Job queued for retry.');
    }

    /**
     * Delete failed job
     */
    public function deleteFailed($id)
    {
        DB::table('failed_jobs')->where('id', $id)->delete();
        
        return redirect()->back()->with('success', 'Failed job deleted.');
    }

    /**
     * Resend failed email
     */
    public function resendFailedEmail($id)
    {
        $campaignLead = \App\Models\CampaignLead::find($id);
        
        if (!$campaignLead) {
            return redirect()->back()->with('error', 'Campaign lead not found.');
        }

        // Check if lead has email
        $lead = $campaignLead->lead;
        if (!$lead || !$lead->email) {
            return redirect()->back()->with('error', 'Lead has no email address.');
        }

        // Reset status to fresh and clear metadata
        $campaignLead->update([
            'status' => 'fresh',
            'metadata' => null,
            'sent_at' => null,
            'bounced_at' => null
        ]);

        // Dispatch the email job again
        try {
            \App\Jobs\SendCampaignEmail::dispatch($campaignLead);
            return redirect()->back()->with('success', 'Failed email queued for resending.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to resend email for CampaignLead ID: {$id}. Error: {$e->getMessage()}");
            return redirect()->back()->with('error', 'Failed to queue email for resending: ' . $e->getMessage());
        }
    }

    /**
     * Delete failed email record
     */
    public function deleteFailedEmail($id)
    {
        $campaignLead = \App\Models\CampaignLead::find($id);
        
        if (!$campaignLead) {
            return redirect()->back()->with('error', 'Campaign lead not found.');
        }

        // Delete the campaign lead record
        $campaignLead->delete();
        
        return redirect()->back()->with('success', 'Failed email record deleted successfully.');
    }
}
