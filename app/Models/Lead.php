<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'phones',
        'company',
        'category',
        'address',
        'full_address',
        'street',
        'municipality',
        'featured_image',
        'bing_maps_url',
        'google_maps_url',
        'latitude',
        'longitude',
        'rating',
        'average_rating',
        'rating_info',
        'review_count',
        'review_url',
        'open_hours',
        'opening_hours',
        'website',
        'domain',
        'social_medias',
        'facebook',
        'instagram',
        'twitter',
        'notes',
        'is_active',
        'last_contacted_at',
        'contact_count',
        'claimed',
        'cid',
        'place_id',
        'kgmid',
        'plus_code',
        'google_knowledge_url'
    ];

    /**
     * Validation rules for lead creation
     */
    public static function validationRules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'category' => 'required|string|max:100',
        ];
    }

    protected $casts = [
        'phones' => 'array',
        'social_medias' => 'array',
        'is_active' => 'boolean',
        'claimed' => 'boolean',
        'last_contacted_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'rating' => 'decimal:2',
        'average_rating' => 'decimal:2'
    ];

    /**
     * Get the category that owns the lead
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category', 'name');
    }

    /**
     * Get the campaigns that the lead belongs to
     */
    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class, 'campaign_leads')
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
     * Get the responses for the lead
     */
    public function responses(): HasMany
    {
        return $this->hasMany(Response::class);
    }

    /**
     * Scope for active leads
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for leads by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for leads by municipality
     */
    public function scopeByMunicipality($query, $municipality)
    {
        return $query->where('municipality', $municipality);
    }

    /**
     * Scope for claimed leads
     */
    public function scopeClaimed($query)
    {
        return $query->where('claimed', true);
    }

    /**
     * Scope for unclaimed leads
     */
    public function scopeUnclaimed($query)
    {
        return $query->where('claimed', false);
    }

    /**
     * Check if lead is duplicate by email or phone
     */
    public static function isDuplicate($email, $phone)
    {
        return static::where(function($query) use ($email, $phone) {
            if ($email) {
                $query->where('email', $email);
            }
            if ($phone) {
                $query->orWhere('phone', $phone);
            }
        })->exists();
    }

    /**
     * Get full address
     */
    public function getFullAddressAttribute()
    {
        return $this->full_address ?: $this->address;
    }

    /**
     * Get primary phone
     */
    public function getPrimaryPhoneAttribute()
    {
        return $this->phone ?: (is_array($this->phones) ? $this->phones[0] ?? null : null);
    }

    /**
     * Get all phone numbers as array
     */
    public function getAllPhonesAttribute()
    {
        $phones = [];
        if ($this->phone) {
            $phones[] = $this->phone;
        }
        if (is_array($this->phones)) {
            $phones = array_merge($phones, $this->phones);
        }
        return array_unique(array_filter($phones));
    }

    /**
     * Get social media links
     */
    public function getSocialMediaLinksAttribute()
    {
        $links = [];
        if ($this->facebook) $links['facebook'] = $this->facebook;
        if ($this->instagram) $links['instagram'] = $this->instagram;
        if ($this->twitter) $links['twitter'] = $this->twitter;
        if ($this->yelp) $links['yelp'] = $this->yelp;
        return $links;
    }

    /**
     * Update contact count and last contacted time
     */
    public function markAsContacted()
    {
        $this->increment('contact_count');
        $this->update(['last_contacted_at' => now()]);
    }

    /**
     * Get response rate for this lead
     */
    public function getResponseRateAttribute()
    {
        $totalCampaigns = $this->campaigns()->count();
        $respondedCampaigns = $this->campaigns()->wherePivot('status', 'responded')->count();

        return $totalCampaigns > 0 ? round(($respondedCampaigns / $totalCampaigns) * 100, 2) : 0;
    }
}
