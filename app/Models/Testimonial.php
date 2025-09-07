<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Testimonial extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'role',
        'company',
        'email',
        'content',
        'rating',
        'project_type',
        'project_name',
        'image',
        'location',
        'is_featured',
        'is_verified',
        'is_active',
        'sort_order',
        'metadata',
        'submitted_at'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_featured' => 'boolean',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'rating' => 'integer',
        'sort_order' => 'integer',
        'metadata' => 'array',
        'submitted_at' => 'datetime',
    ];

    /**
     * Scope a query to only include active testimonials.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured testimonials.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include verified testimonials.
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope a query to filter by project type.
     */
    public function scopeByProjectType(Builder $query, string $projectType): Builder
    {
        return $query->where('project_type', $projectType);
    }

    /**
     * Scope a query to filter by rating.
     */
    public function scopeByRating(Builder $query, int $rating): Builder
    {
        return $query->where('rating', '>=', $rating);
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc');
    }

    /**
     * Get the default image if none is provided.
     */
    public function getImageAttribute($value)
    {
        if ($value) {
            return $value;
        }

        // Return a default avatar based on name initials
        $initials = strtoupper(substr($this->name, 0, 2));
        return "https://ui-avatars.com/api/?name={$initials}&background=3B82F6&color=ffffff&size=200";
    }

    /**
     * Get the project type display name.
     */
    public function getProjectTypeDisplayAttribute()
    {
        $types = [
            'web-development' => 'Web Development',
            'mobile-app' => 'Mobile App',
            'ui-ux-design' => 'UI/UX Design',
            'e-commerce' => 'E-commerce',
            'consulting' => 'Consulting',
            'seo' => 'SEO Services',
            'digital-marketing' => 'Digital Marketing',
            'other' => 'Other'
        ];

        return $types[$this->project_type] ?? 'Other';
    }

    /**
     * Get the average rating for all active testimonials.
     */
    public static function getAverageRating()
    {
        return self::active()->avg('rating') ?? 0;
    }

    /**
     * Get the total count of active testimonials.
     */
    public static function getTotalCount()
    {
        return self::active()->count();
    }

    /**
     * Get testimonials grouped by rating.
     */
    public static function getRatingDistribution()
    {
        return self::active()
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get()
            ->pluck('count', 'rating')
            ->toArray();
    }

    /**
     * Get testimonials grouped by project type.
     */
    public static function getProjectTypeDistribution()
    {
        return self::active()
            ->selectRaw('project_type, COUNT(*) as count')
            ->groupBy('project_type')
            ->orderBy('count', 'desc')
            ->get()
            ->pluck('count', 'project_type')
            ->toArray();
    }
}
