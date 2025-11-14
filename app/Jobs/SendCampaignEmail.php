<?php

namespace App\Jobs;

use App\Models\CampaignLead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class SendCampaignEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public CampaignLead $campaignLead
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $campaign = $this->campaignLead->campaign;
            $lead = $this->campaignLead->lead;

            // Check if lead has email
            if (!$lead->email) {
                Log::warning("Lead {$lead->id} has no email address");
                $this->campaignLead->markAsBounced();
                return;
            }

            // Check if already sent
            if ($this->campaignLead->status !== 'fresh') {
                Log::info("Campaign lead {$this->campaignLead->id} already processed");
                return;
            }

            // Send email
            Mail::raw($campaign->email_body, function ($message) use ($campaign, $lead) {
                $message->to($lead->email, $lead->name)
                        ->subject($campaign->email_subject)
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });

            // Mark as sent
            $this->campaignLead->markAsSent(
                $this->campaignLead->email_address ?: $lead->email,
                $campaign->email_subject,
                $campaign->email_body
            );

            // Update campaign stats
            $campaign->updateStats();

            Log::info("Campaign email sent successfully to lead {$lead->id}");

        } catch (Exception $e) {
            Log::error("Failed to send campaign email: " . $e->getMessage());

            // Mark as failed in metadata
            $metadata = $this->campaignLead->metadata ?? [];
            $metadata['last_error'] = $e->getMessage();
            $metadata['failed_at'] = now()->toDateTimeString();
            $this->campaignLead->update(['metadata' => $metadata]);

            throw $e; // Re-throw to trigger retry
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception): void
    {
        Log::error("Campaign email job failed permanently: " . $exception->getMessage());

        // Update campaign failed count
        $campaign = $this->campaignLead->campaign;
        $campaign->increment('emails_failed');

        // Mark in metadata
        $metadata = $this->campaignLead->metadata ?? [];
        $metadata['failed_permanently'] = true;
        $metadata['failure_reason'] = $exception->getMessage();
        $this->campaignLead->update(['metadata' => $metadata]);
    }
}
