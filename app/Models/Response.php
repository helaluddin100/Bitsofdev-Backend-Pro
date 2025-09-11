<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Response extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'campaign_id',
        'campaign_lead_id',
        'response_date',
        'response_message',
        'response_type',
        'sentiment',
        'is_qualified',
        'notes'
    ];

    protected $casts = [
        'response_date' => 'datetime',
        'is_qualified' => 'boolean'
    ];

    /**
     * Get the lead that owns the response
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Get the campaign that owns the response
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Get the campaign lead that owns the response
     */
    public function campaignLead(): BelongsTo
    {
        return $this->belongsTo(CampaignLead::class);
    }

    /**
     * Scope for qualified responses
     */
    public function scopeQualified($query)
    {
        return $query->where('is_qualified', true);
    }

    /**
     * Scope for responses by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('response_type', $type);
    }

    /**
     * Scope for responses by sentiment
     */
    public function scopeBySentiment($query, $sentiment)
    {
        return $query->where('sentiment', $sentiment);
    }

    /**
     * Scope for positive responses
     */
    public function scopePositive($query)
    {
        return $query->where('sentiment', 'positive');
    }

    /**
     * Scope for negative responses
     */
    public function scopeNegative($query)
    {
        return $query->where('sentiment', 'negative');
    }

    /**
     * Get sentiment badge color
     */
    public function getSentimentBadgeColor()
    {
        return match($this->sentiment) {
            'positive' => 'success',
            'neutral' => 'info',
            'negative' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get response type display name
     */
    public function getResponseTypeDisplayName()
    {
        return match($this->response_type) {
            'email' => 'Email Reply',
            'phone' => 'Phone Call',
            'website' => 'Website Contact',
            'social' => 'Social Media',
            'in_person' => 'In Person',
            default => ucfirst($this->response_type)
        };
    }

    /**
     * Get sentiment display name
     */
    public function getSentimentDisplayName()
    {
        return match($this->sentiment) {
            'positive' => 'Positive',
            'neutral' => 'Neutral',
            'negative' => 'Negative',
            default => 'Unknown'
        };
    }

    /**
     * Mark as qualified
     */
    public function markAsQualified()
    {
        $this->update(['is_qualified' => true]);
    }

    /**
     * Mark as unqualified
     */
    public function markAsUnqualified()
    {
        $this->update(['is_qualified' => false]);
    }

    /**
     * Get response summary
     */
    public function getSummaryAttribute()
    {
        $summary = "Response from {$this->lead->name}";

        if ($this->response_type) {
            $summary .= " via {$this->getResponseTypeDisplayName()}";
        }

        if ($this->sentiment) {
            $summary .= " ({$this->getSentimentDisplayName()})";
        }

        if ($this->is_qualified) {
            $summary .= " - Qualified Lead";
        }

        return $summary;
    }

    /**
     * Get response preview (first 100 characters)
     */
    public function getPreviewAttribute()
    {
        return strlen($this->response_message) > 100
            ? substr($this->response_message, 0, 100) . '...'
            : $this->response_message;
    }
}
