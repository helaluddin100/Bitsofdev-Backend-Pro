<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampaignLead extends Model
{
    use HasFactory;

    protected $table = 'campaign_leads';

    protected $fillable = [
        'campaign_id',
        'lead_id',
        'status',
        'sent_at',
        'reminder_1_sent_at',
        'reminder_2_sent_at',
        'reminder_3_sent_at',
        'responded_at',
        'bounced_at',
        'unsubscribed_at',
        'email_address',
        'email_subject',
        'email_body',
        'response_message',
        'response_source',
        'metadata',
        'notes'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'reminder_1_sent_at' => 'datetime',
        'reminder_2_sent_at' => 'datetime',
        'reminder_3_sent_at' => 'datetime',
        'responded_at' => 'datetime',
        'bounced_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'metadata' => 'array'
    ];

    /**
     * Get the campaign that owns the campaign lead
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Get the lead that owns the campaign lead
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Get the responses for this campaign lead
     */
    public function responses(): HasMany
    {
        return $this->hasMany(Response::class, 'campaign_lead_id');
    }

    /**
     * Scope for fresh leads
     */
    public function scopeFresh($query)
    {
        return $query->where('status', 'fresh');
    }

    /**
     * Scope for sent leads
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope for responded leads
     */
    public function scopeResponded($query)
    {
        return $query->where('status', 'responded');
    }

    /**
     * Scope for leads ready for reminder 1
     */
    public function scopeReadyForReminder1($query, $days = 3)
    {
        $cutoffDate = now()->subDays($days);

        return $query->where('status', 'sent')
                    ->where('sent_at', '<=', $cutoffDate)
                    ->whereNull('reminder_1_sent_at');
    }

    /**
     * Scope for leads ready for reminder 2
     */
    public function scopeReadyForReminder2($query, $days = 7)
    {
        $cutoffDate = now()->subDays($days);

        return $query->where('status', 'reminder_1')
                    ->where('reminder_1_sent_at', '<=', $cutoffDate)
                    ->whereNull('reminder_2_sent_at');
    }

    /**
     * Scope for leads ready for reminder 3
     */
    public function scopeReadyForReminder3($query, $days = 10)
    {
        $cutoffDate = now()->subDays($days);

        return $query->where('status', 'reminder_2')
                    ->where('reminder_2_sent_at', '<=', $cutoffDate)
                    ->whereNull('reminder_3_sent_at');
    }

    /**
     * Mark as sent
     */
    public function markAsSent($emailAddress = null, $emailSubject = null, $emailBody = null)
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'email_address' => $emailAddress,
            'email_subject' => $emailSubject,
            'email_body' => $emailBody
        ]);

        // Update lead contact count
        $this->lead->markAsContacted();
    }

    /**
     * Mark as reminder 1 sent
     */
    public function markAsReminder1Sent()
    {
        $this->update([
            'status' => 'reminder_1',
            'reminder_1_sent_at' => now()
        ]);
    }

    /**
     * Mark as reminder 2 sent
     */
    public function markAsReminder2Sent()
    {
        $this->update([
            'status' => 'reminder_2',
            'reminder_2_sent_at' => now()
        ]);
    }

    /**
     * Mark as reminder 3 sent
     */
    public function markAsReminder3Sent()
    {
        $this->update([
            'status' => 'reminder_3',
            'reminder_3_sent_at' => now()
        ]);
    }

    /**
     * Mark as responded
     */
    public function markAsResponded($responseMessage = null, $responseSource = null)
    {
        $this->update([
            'status' => 'responded',
            'responded_at' => now(),
            'response_message' => $responseMessage,
            'response_source' => $responseSource
        ]);
    }

    /**
     * Mark as bounced
     */
    public function markAsBounced()
    {
        $this->update([
            'status' => 'bounced',
            'bounced_at' => now()
        ]);
    }

    /**
     * Mark as unsubscribed
     */
    public function markAsUnsubscribed()
    {
        $this->update([
            'status' => 'unsubscribed',
            'unsubscribed_at' => now()
        ]);
    }

    /**
     * Check if lead has responded
     */
    public function hasResponded()
    {
        return $this->status === 'responded';
    }

    /**
     * Check if lead is ready for reminder
     */
    public function isReadyForReminder($reminderNumber, $days)
    {
        if ($reminderNumber === 1) {
            return $this->status === 'sent' &&
                   $this->sent_at &&
                   $this->sent_at->addDays($days)->isPast() &&
                   !$this->reminder_1_sent_at;
        }

        if ($reminderNumber === 2) {
            return $this->status === 'reminder_1' &&
                   $this->reminder_1_sent_at &&
                   $this->reminder_1_sent_at->addDays($days)->isPast() &&
                   !$this->reminder_2_sent_at;
        }

        if ($reminderNumber === 3) {
            return $this->status === 'reminder_2' &&
                   $this->reminder_2_sent_at &&
                   $this->reminder_2_sent_at->addDays($days)->isPast() &&
                   !$this->reminder_3_sent_at;
        }

        return false;
    }

    /**
     * Get days since last contact
     */
    public function getDaysSinceLastContact()
    {
        $lastContact = $this->reminder_3_sent_at ??
                      $this->reminder_2_sent_at ??
                      $this->reminder_1_sent_at ??
                      $this->sent_at;

        return $lastContact ? $lastContact->diffInDays(now()) : null;
    }

    /**
     * Get next reminder date
     */
    public function getNextReminderDate($reminderNumber, $days)
    {
        if ($reminderNumber === 1) {
            return $this->sent_at ? $this->sent_at->copy()->addDays($days) : null;
        }

        if ($reminderNumber === 2) {
            return $this->reminder_1_sent_at ? $this->reminder_1_sent_at->copy()->addDays($days) : null;
        }

        if ($reminderNumber === 3) {
            return $this->reminder_2_sent_at ? $this->reminder_2_sent_at->copy()->addDays($days) : null;
        }

        return null;
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColor()
    {
        return match($this->status) {
            'fresh' => 'secondary',
            'sent' => 'primary',
            'reminder_1' => 'warning',
            'reminder_2' => 'info',
            'reminder_3' => 'warning',
            'responded' => 'success',
            'bounced' => 'danger',
            'unsubscribed' => 'dark',
            default => 'secondary'
        };
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayName()
    {
        return match($this->status) {
            'fresh' => 'Fresh',
            'sent' => 'Sent',
            'reminder_1' => 'Reminder 1',
            'reminder_2' => 'Reminder 2',
            'reminder_3' => 'Reminder 3',
            'responded' => 'Responded',
            'bounced' => 'Bounced',
            'unsubscribed' => 'Unsubscribed',
            default => 'Unknown'
        };
    }
}
