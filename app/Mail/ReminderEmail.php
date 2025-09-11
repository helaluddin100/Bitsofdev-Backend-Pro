<?php

namespace App\Mail;

use App\Models\Campaign;
use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReminderEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $campaign;
    public $lead;
    public $subject;
    public $body;
    public $reminderNumber;

    /**
     * Create a new message instance.
     */
    public function __construct(Campaign $campaign, Lead $lead, $subject, $body, $reminderNumber)
    {
        $this->campaign = $campaign;
        $this->lead = $lead;
        $this->subject = $subject;
        $this->body = $body;
        $this->reminderNumber = $reminderNumber;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reminder',
            with: [
                'campaign' => $this->campaign,
                'lead' => $this->lead,
                'subject' => $this->subject,
                'body' => $this->body,
                'reminderNumber' => $this->reminderNumber,
                'unsubscribeUrl' => $this->getUnsubscribeUrl(),
                'trackingPixel' => $this->getTrackingPixel()
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Get unsubscribe URL
     */
    protected function getUnsubscribeUrl()
    {
        return route('unsubscribe', [
            'campaign' => $this->campaign->id,
            'lead' => $this->lead->id,
            'token' => $this->generateUnsubscribeToken()
        ]);
    }

    /**
     * Generate unsubscribe token
     */
    protected function generateUnsubscribeToken()
    {
        return hash('sha256', $this->campaign->id . $this->lead->id . config('app.key'));
    }

    /**
     * Get tracking pixel URL
     */
    protected function getTrackingPixel()
    {
        return route('track.email', [
            'campaign' => $this->campaign->id,
            'lead' => $this->lead->id,
            'token' => $this->generateTrackingToken()
        ]);
    }

    /**
     * Generate tracking token
     */
    protected function generateTrackingToken()
    {
        return hash('sha256', $this->campaign->id . $this->lead->id . 'track' . config('app.key'));
    }
}
