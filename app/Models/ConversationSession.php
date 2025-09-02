<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ConversationSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'visitor_ip',
        'user_agent',
        'last_activity',
        'message_count',
        'is_active'
    ];

    protected $casts = [
        'last_activity' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function messages()
    {
        return $this->hasMany(ConversationMessage::class)->orderBy('created_at');
    }

    public function recentMessages($limit = 10)
    {
        return $this->messages()->latest()->limit($limit)->get()->reverse();
    }

    public function updateActivity()
    {
        $this->update([
            'last_activity' => now(),
            'message_count' => $this->message_count + 1
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('last_activity', '>=', Carbon::now()->subHours($hours));
    }

    public static function getOrCreateSession($sessionId, $visitorIp = null, $userAgent = null)
    {
        $session = self::where('session_id', $sessionId)
            ->where('is_active', true)
            ->first();

        if (!$session) {
            $session = self::create([
                'session_id' => $sessionId,
                'visitor_ip' => $visitorIp,
                'user_agent' => $userAgent,
                'last_activity' => now(),
                'message_count' => 0,
                'is_active' => true
            ]);
        } else {
            $session->updateActivity();
        }

        return $session;
    }
}
