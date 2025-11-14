<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'email_subject',
        'email_body',
        'schedule_type',
        'scheduled_at',
        'sent_at',
        'status',
        'total_leads',
        'emails_sent',
        'sent_count',
        'reminder_1_sent',
        'reminder_2_sent',
        'emails_failed',
        'responses_received',
        'response_count',
        'response_rate',
        'is_active',
        'notes',
        'settings',
        'reminders_enabled',
        'enable_reminders',
        'reminder_days_1',
        'reminder_1_days',
        'reminder_days_2',
        'reminder_2_days',
        'reminder_subject_1',
        'reminder_1_subject',
        'reminder_body_1',
        'reminder_1_body',
        'reminder_subject_2',
        'reminder_2_subject',
        'reminder_body_2',
        'reminder_2_body',
        'reminder_3_days',
        'reminder_3_subject',
        'reminder_3_body'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'is_active' => 'boolean',
        'reminders_enabled' => 'boolean',
        'enable_reminders' => 'boolean',
        'settings' => 'array',
        'response_rate' => 'decimal:2'
    ];

    /**
     * Get the leads that belong to the campaign
     */
    public function leads(): BelongsToMany
    {
        return $this->belongsToMany(Lead::class, 'campaign_leads')
                    ->withPivot([
                        'status',
                        'sent_at',
                        'reminder_1_sent_at',
                        'reminder_2_sent_at',
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
                    ])
                    ->withTimestamps();
    }

    /**
     * Get the campaign leads pivot records
     */
    public function campaignLeads(): HasMany
    {
        return $this->hasMany(CampaignLead::class);
    }

    /**
     * Get the responses for the campaign
     */
    public function responses(): HasMany
    {
        return $this->hasMany(Response::class);
    }

    /**
     * Scope for active campaigns
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for campaigns by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for campaigns by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for scheduled campaigns
     */
    public function scopeScheduled($query)
    {
        return $query->where('schedule_type', 'scheduled')
                    ->where('status', 'scheduled');
    }

    /**
     * Scope for ready to send campaigns
     */
    public function scopeReadyToSend($query)
    {
        return $query->where('status', 'scheduled')
                    ->where('scheduled_at', '<=', now());
    }

    /**
     * Get campaign statistics
     */
    public function getStatsAttribute()
    {
        return [
            'total_leads' => $this->total_leads,
            'sent_count' => $this->sent_count,
            'reminder_1_sent' => $this->reminder_1_sent,
            'reminder_2_sent' => $this->reminder_2_sent,
            'response_count' => $this->response_count,
            'response_rate' => $this->response_rate,
            'emails_failed' => $this->emails_failed
        ];
    }

    /**
     * Get leads by status
     */
    public function getLeadsByStatus($status)
    {
        return $this->leads()->wherePivot('status', $status)->get();
    }

    /**
     * Get fresh leads (not sent yet)
     */
    public function getFreshLeads()
    {
        return $this->getLeadsByStatus('fresh');
    }

    /**
     * Get sent leads
     */
    public function getSentLeads()
    {
        return $this->getLeadsByStatus('sent');
    }

    /**
     * Get responded leads
     */
    public function getRespondedLeads()
    {
        return $this->getLeadsByStatus('responded');
    }

    /**
     * Get leads ready for reminder 1
     */
    public function getLeadsForReminder1()
    {
        $days = $this->reminder_1_days ?? $this->reminder_days_1 ?? 3;
        $cutoffDate = now()->subDays($days);

        return $this->leads()
            ->wherePivot('status', 'sent')
            ->wherePivot('sent_at', '<=', $cutoffDate)
            ->wherePivot('reminder_1_sent_at', null)
            ->get();
    }

    /**
     * Get leads ready for reminder 2
     */
    public function getLeadsForReminder2()
    {
        $days = $this->reminder_2_days ?? $this->reminder_days_2 ?? 7;
        $cutoffDate = now()->subDays($days);

        return $this->leads()
            ->wherePivot('status', 'reminder_1')
            ->wherePivot('reminder_1_sent_at', '<=', $cutoffDate)
            ->wherePivot('reminder_2_sent_at', null)
            ->get();
    }

    /**
     * Update campaign statistics
     */
    public function updateStats()
    {
        $this->update([
            'total_leads' => $this->leads()->count(),
            'sent_count' => $this->leads()->wherePivot('status', 'sent')->count(),
            'reminder_1_sent' => $this->leads()->wherePivot('status', 'reminder_1')->count(),
            'reminder_2_sent' => $this->leads()->wherePivot('status', 'reminder_2')->count(),
            'response_count' => $this->leads()->wherePivot('status', 'responded')->count(),
            'response_rate' => $this->calculateResponseRate()
        ]);
    }

    /**
     * Calculate response rate
     */
    public function calculateResponseRate()
    {
        $totalSent = $this->sent_count;
        $responses = $this->response_count;

        return $totalSent > 0 ? round(($responses / $totalSent) * 100, 2) : 0;
    }

    /**
     * Check if campaign can be sent
     */
    public function canBeSent()
    {
        return $this->status === 'draft' ||
               ($this->status === 'scheduled' && $this->scheduled_at <= now());
    }

    /**
     * Mark campaign as sent
     */
    public function markAsSent()
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now()
        ]);
    }

    /**
     * Mark campaign as completed
     */
    public function markAsCompleted()
    {
        $this->update(['status' => 'completed']);
    }

    /**
     * Get reminder subject for reminder number
     */
    public function getReminderSubject($reminderNumber)
    {
        if ($reminderNumber === 1) {
            return $this->reminder_1_subject ?? $this->reminder_subject_1 ?? 'Follow-up: ' . $this->email_subject;
        }

        if ($reminderNumber === 2) {
            return $this->reminder_2_subject ?? $this->reminder_subject_2 ?? 'Final Follow-up: ' . $this->email_subject;
        }

        if ($reminderNumber === 3) {
            return $this->reminder_3_subject ?? 'Last Reminder: ' . $this->email_subject;
        }

        return $this->email_subject;
    }

    /**
     * Get reminder body for reminder number
     */
    public function getReminderBody($reminderNumber)
    {
        if ($reminderNumber === 1) {
            return $this->reminder_1_body ?? $this->reminder_body_1 ?? $this->email_body;
        }

        if ($reminderNumber === 2) {
            return $this->reminder_2_body ?? $this->reminder_body_2 ?? $this->email_body;
        }

        if ($reminderNumber === 3) {
            return $this->reminder_3_body ?? $this->email_body;
        }

        return $this->email_body;
    }
}
