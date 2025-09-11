<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function blogs(): HasMany
    {
        return $this->hasMany(Blog::class);
    }

    /**
     * Get the leads for this category
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'category', 'name');
    }

    /**
     * Get the campaigns for this category
     */
    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class, 'category', 'name');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get leads count for this category
     */
    public function getLeadsCountAttribute()
    {
        return $this->leads()->count();
    }

    /**
     * Get campaigns count for this category
     */
    public function getCampaignsCountAttribute()
    {
        return $this->campaigns()->count();
    }

    /**
     * Get response rate for this category
     */
    public function getResponseRateAttribute()
    {
        $totalLeads = $this->leads()->count();
        $respondedLeads = $this->leads()->whereHas('campaigns', function($query) {
            $query->wherePivot('status', 'responded');
        })->count();

        return $totalLeads > 0 ? round(($respondedLeads / $totalLeads) * 100, 2) : 0;
    }
}
