<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Campaign;
use App\Models\CampaignLead;
use App\Jobs\SendCampaignEmail;
use App\Jobs\SendReminderEmail;
use Illuminate\Support\Facades\Log;

class ProcessCampaignEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaigns:process-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process campaign emails and send reminders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing campaign emails...');

        // Process campaigns ready to send
        $this->processReadyCampaigns();

        // Process reminders
        $this->processReminders();

        $this->info('Campaign email processing completed.');
    }

    /**
     * Process campaigns ready to send
     */
    protected function processReadyCampaigns()
    {
        $campaigns = Campaign::where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->orWhere(function($query) {
                $query->where('status', 'draft')
                      ->where('schedule_type', 'immediate');
            })
            ->get();

        foreach ($campaigns as $campaign) {
            // Get fresh leads
            $freshLeads = $campaign->leads()->wherePivot('status', 'fresh')->get();

            foreach ($freshLeads as $lead) {
                $campaignLead = CampaignLead::where('campaign_id', $campaign->id)
                    ->where('lead_id', $lead->id)
                    ->first();

                if ($campaignLead && $campaignLead->status === 'fresh') {
                    SendCampaignEmail::dispatch($campaignLead);
                    $this->info("Queued email for lead: {$lead->name}");
                }
            }

            // Update campaign status
            if ($campaign->status === 'draft') {
                $campaign->update(['status' => 'sending']);
            }
        }
    }

    /**
     * Process reminders
     */
    protected function processReminders()
    {
        $campaigns = Campaign::where('enable_reminders', true)
            ->orWhere('reminders_enabled', true)
            ->get();

        foreach ($campaigns as $campaign) {
            // Reminder 1
            if ($campaign->reminder_1_days) {
                $leadsForReminder1 = CampaignLead::where('campaign_id', $campaign->id)
                    ->readyForReminder1($campaign->reminder_1_days)
                    ->get();

                foreach ($leadsForReminder1 as $campaignLead) {
                    if (!$campaignLead->hasResponded()) {
                        SendReminderEmail::dispatch($campaignLead, 1);
                        $this->info("Queued reminder 1 for lead: {$campaignLead->lead->name}");
                    }
                }
            }

            // Reminder 2
            if ($campaign->reminder_2_days) {
                $leadsForReminder2 = CampaignLead::where('campaign_id', $campaign->id)
                    ->readyForReminder2($campaign->reminder_2_days)
                    ->get();

                foreach ($leadsForReminder2 as $campaignLead) {
                    if (!$campaignLead->hasResponded()) {
                        SendReminderEmail::dispatch($campaignLead, 2);
                        $this->info("Queued reminder 2 for lead: {$campaignLead->lead->name}");
                    }
                }
            }

            // Reminder 3
            if ($campaign->reminder_3_days) {
                $leadsForReminder3 = CampaignLead::where('campaign_id', $campaign->id)
                    ->readyForReminder3($campaign->reminder_3_days)
                    ->get();

                foreach ($leadsForReminder3 as $campaignLead) {
                    if (!$campaignLead->hasResponded()) {
                        SendReminderEmail::dispatch($campaignLead, 3);
                        $this->info("Queued reminder 3 for lead: {$campaignLead->lead->name}");
                    }
                }
            }
        }
    }
}
