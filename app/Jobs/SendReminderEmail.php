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

class SendReminderEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public CampaignLead $campaignLead,
        public int $reminderNumber
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
                Log::warning("Lead {$lead->id} has no email address for reminder");
                return;
            }

            // Check if already responded
            if ($this->campaignLead->hasResponded()) {
                Log::info("Lead {$lead->id} already responded, skipping reminder");
                return;
            }

            // Get reminder content
            $subject = $campaign->getReminderSubject($this->reminderNumber);
            $body = $campaign->getReminderBody($this->reminderNumber);

            // Send email
            Mail::raw($body, function ($message) use ($subject, $lead) {
                $message->to($lead->email, $lead->name)
                        ->subject($subject)
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });

            // Mark reminder as sent
            if ($this->reminderNumber === 1) {
                $this->campaignLead->markAsReminder1Sent();
            } elseif ($this->reminderNumber === 2) {
                $this->campaignLead->markAsReminder2Sent();
            } elseif ($this->reminderNumber === 3) {
                $this->campaignLead->update([
                    'status' => 'reminder_3',
                    'reminder_3_sent_at' => now()
                ]);
            }

            // Update campaign stats
            $campaign->updateStats();

            Log::info("Reminder {$this->reminderNumber} sent successfully to lead {$lead->id}");

        } catch (Exception $e) {
            Log::error("Failed to send reminder email: " . $e->getMessage());
            
            // Mark as failed in metadata
            $metadata = $this->campaignLead->metadata ?? [];
            $metadata['reminder_' . $this->reminderNumber . '_error'] = $e->getMessage();
            $metadata['reminder_' . $this->reminderNumber . '_failed_at'] = now()->toDateTimeString();
            $this->campaignLead->update(['metadata' => $metadata]);
            
            throw $e; // Re-throw to trigger retry
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception): void
    {
        Log::error("Reminder email job failed permanently: " . $exception->getMessage());
        
        // Update campaign failed count
        $campaign = $this->campaignLead->campaign;
        $campaign->increment('emails_failed');
        
        // Mark in metadata
        $metadata = $this->campaignLead->metadata ?? [];
        $metadata['reminder_' . $this->reminderNumber . '_failed_permanently'] = true;
        $metadata['reminder_' . $this->reminderNumber . '_failure_reason'] = $exception->getMessage();
        $this->campaignLead->update(['metadata' => $metadata]);
    }
}
