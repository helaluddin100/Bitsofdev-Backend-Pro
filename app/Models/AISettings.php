<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AISettings extends Model
{
    use HasFactory;

    protected $table = 'a_i_settings';

    protected $fillable = [
        'ai_provider',
        'training_mode',
        'learning_threshold',
        'use_static_responses',
        'ai_config'
    ];

    protected $casts = [
        'training_mode' => 'boolean',
        'use_static_responses' => 'boolean',
        'ai_config' => 'array'
    ];

    /**
     * Get current AI settings (singleton pattern)
     */
    public static function getCurrent()
    {
        return static::first() ?? static::create([
            'ai_provider' => 'gemini',
            'training_mode' => false,
            'learning_threshold' => 10,
            'use_static_responses' => false
        ]);
    }

    /**
     * Update AI settings
     */
    public static function updateSettings($data)
    {
        $settings = static::getCurrent();
        $settings->update($data);
        return $settings;
    }
}
