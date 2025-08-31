<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class About extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'hero_title',
        'hero_description',
        'story_title',
        'story_content',
        'mission_title',
        'mission_description',
        'vision_title',
        'vision_description',
        'years_experience',
        'projects_delivered',
        'happy_clients',
        'support_availability',
        'values_title',
        'values_description',
        'process_title',
        'process_description',
        'team_title',
        'team_description',
        'cta_title',
        'cta_description',
        'is_active'
    ];

    protected $casts = [
        'years_experience' => 'integer',
        'projects_delivered' => 'integer',
        'happy_clients' => 'integer',
        'is_active' => 'boolean',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(CompanyValue::class);
    }

    public function processes(): HasMany
    {
        return $this->hasMany(CompanyProcess::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
