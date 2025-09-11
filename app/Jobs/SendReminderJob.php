<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\CampaignLead;
use App\Mail\ReminderEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaignLead;
    protected $reminderNumber;

    /**
     * Create a new job instance.
     */
    public function __construct(CampaignLead $campaignLead, $reminderNumber)
    {
        $this->campaignLead = $campaignLead;
        $this->reminderNumber = $reminderNumber;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $lead = $this->campaignLead->lead;
            $campaign = $this->campaignLead->campaign;

            // Check if lead has valid email
            if (!$lead->email) {
                Log::warning("Lead {$lead->id} has no email address, skipping reminder.");
                return;
            }

            // Check if lead has already responded
            if ($this->campaignLead->hasResponded()) {
                Log::info("Lead {$lead->id} has already responded, skipping reminder.");
                return;
            }

            // Get reminder content
            $subject = $campaign->getReminderSubject($this->reminderNumber);
            $body = $campaign->getReminderBody($this->reminderNumber);

            // Send reminder email
            Mail::to($lead->email)->send(new ReminderEmail($campaign, $lead, $subject, $body, $this->reminderNumber));

            // Update campaign lead status
            if ($this->reminderNumber === 1) {
                $this->campaignLead->markAsReminder1Sent();
                $campaign->increment('reminder_1_sent');
            } elseif ($this->reminderNumber === 2) {
                $this->campaignLead->markAsReminder2Sent();
                $campaign->increment('reminder_2_sent');
            }

            // Update lead contact count
            $lead->markAsContacted();

            Log::info("Reminder {$this->reminderNumber} sent to {$lead->email} for campaign {$campaign->id}");

        } catch (\Exception $e) {
            Log::error("Error sending reminder {$this->reminderNumber} to lead {$this->campaignLead->lead_id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SendReminderJob failed for campaign lead {$this->campaignLead->id}: " . $exception->getMessage());
    }
}
