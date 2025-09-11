<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\CampaignLead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Starting reminder processing job...');

            // Get all active campaigns with reminders enabled
            $campaigns = Campaign::where('is_active', true)
                ->where('enable_reminders', true)
                ->where('status', 'sent')
                ->get();

            foreach ($campaigns as $campaign) {
                $this->processCampaignReminders($campaign);
            }

            Log::info('Reminder processing job completed successfully.');

        } catch (\Exception $e) {
            Log::error('Error in ProcessRemindersJob: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Process reminders for a specific campaign
     */
    protected function processCampaignReminders(Campaign $campaign)
    {
        try {
            // Process reminder 1
            $this->processReminder1($campaign);

            // Process reminder 2
            $this->processReminder2($campaign);

        } catch (\Exception $e) {
            Log::error("Error processing reminders for campaign {$campaign->id}: " . $e->getMessage());
        }
    }

    /**
     * Process reminder 1 for a campaign
     */
    protected function processReminder1(Campaign $campaign)
    {
        $reminderDays = $campaign->reminder_1_days ?? $campaign->reminder_days_1 ?? 3;
        $leadsForReminder1 = $campaign->getLeadsForReminder1();

        if ($leadsForReminder1->isEmpty()) {
            return;
        }

        Log::info("Processing reminder 1 for campaign {$campaign->id}: {$leadsForReminder1->count()} leads");

        foreach ($leadsForReminder1 as $lead) {
            $campaignLead = CampaignLead::where('campaign_id', $campaign->id)
                ->where('lead_id', $lead->id)
                ->first();

            if ($campaignLead && $campaignLead->isReadyForReminder(1, $reminderDays)) {
                SendReminderJob::dispatch($campaignLead, 1);
            }
        }
    }

    /**
     * Process reminder 2 for a campaign
     */
    protected function processReminder2(Campaign $campaign)
    {
        $reminderDays = $campaign->reminder_2_days ?? $campaign->reminder_days_2 ?? 7;
        $leadsForReminder2 = $campaign->getLeadsForReminder2();

        if ($leadsForReminder2->isEmpty()) {
            return;
        }

        Log::info("Processing reminder 2 for campaign {$campaign->id}: {$leadsForReminder2->count()} leads");

        foreach ($leadsForReminder2 as $lead) {
            $campaignLead = CampaignLead::where('campaign_id', $campaign->id)
                ->where('lead_id', $lead->id)
                ->first();

            if ($campaignLead && $campaignLead->isReadyForReminder(2, $reminderDays)) {
                SendReminderJob::dispatch($campaignLead, 2);
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessRemindersJob failed: ' . $exception->getMessage());
    }
}
