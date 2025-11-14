<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\CampaignLead;
use App\Mail\CampaignEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign;
    protected $batchSize;

    /**
     * Create a new job instance.
     */
    public function __construct(Campaign $campaign, $batchSize = 50)
    {
        $this->campaign = $campaign;
        $this->batchSize = $batchSize;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Update campaign status to sending
            $this->campaign->update(['status' => 'sending']);

            // Get fresh leads for this campaign
            $freshLeads = $this->campaign->getFreshLeads();

            if ($freshLeads->isEmpty()) {
                $this->campaign->update(['status' => 'sent']);
                Log::info("Campaign {$this->campaign->id} has no fresh leads to send.");
                return;
            }

            // Process leads in batches
            $chunks = $freshLeads->chunk($this->batchSize);
            foreach ($chunks as $leads) {
                foreach ($leads as $lead) {
                    $this->sendEmailToLead($lead);
                }
            }

            // Update campaign status to sent
            $this->campaign->markAsSent();
            $this->campaign->updateStats();

            Log::info("Campaign {$this->campaign->id} sent successfully to {$freshLeads->count()} leads.");
        } catch (\Exception $e) {
            Log::error("Error sending campaign {$this->campaign->id}: " . $e->getMessage());
            $this->campaign->update(['status' => 'paused']);
            throw $e;
        }
    }

    /**
     * Send email to a specific lead
     */
    protected function sendEmailToLead($lead)
    {
        try {
            // Get the campaign lead pivot record
            $campaignLead = CampaignLead::where('campaign_id', $this->campaign->id)
                ->where('lead_id', $lead->id)
                ->first();

            if (!$campaignLead) {
                Log::warning("Campaign lead not found for campaign {$this->campaign->id} and lead {$lead->id}");
                return;
            }

            // Check if lead has valid email
            if (!$lead->email) {
                Log::warning("Lead {$lead->id} has no email address, skipping.");
                $campaignLead->markAsBounced();
                return;
            }

            // Send email
            Mail::to($lead->email)->send(new CampaignEmail($this->campaign, $lead));

            // Mark as sent
            $campaignLead->markAsSent(
                $lead->email,
                $this->campaign->email_subject,
                $this->campaign->email_body
            );

            // Update campaign counters
            $this->campaign->increment('emails_sent');
            $this->campaign->increment('sent_count');

            Log::info("Email sent to {$lead->email} for campaign {$this->campaign->id}");
        } catch (\Exception $e) {
            Log::error("Error sending email to lead {$lead->id}: " . $e->getMessage());

            // Mark as bounced
            $campaignLead = CampaignLead::where('campaign_id', $this->campaign->id)
                ->where('lead_id', $lead->id)
                ->first();

            if ($campaignLead) {
                $campaignLead->markAsBounced();
            }

            // Update campaign counters
            $this->campaign->increment('emails_failed');
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SendCampaignJob failed for campaign {$this->campaign->id}: " . $exception->getMessage());
        $this->campaign->update(['status' => 'paused']);
    }
}
