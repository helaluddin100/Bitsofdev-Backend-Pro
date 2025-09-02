<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversationMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_session_id',
        'sender',
        'message',
        'message_type',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function conversationSession()
    {
        return $this->belongsTo(ConversationSession::class);
    }

    public function scopeVisitor($query)
    {
        return $query->where('sender', 'visitor');
    }

    public function scopeAI($query)
    {
        return $query->where('sender', 'ai');
    }

    public function scopeRecent($query, $limit = 10)
    {
        return $query->latest()->limit($limit);
    }

    public static function addMessage($sessionId, $sender, $message, $messageType = 'text', $metadata = null)
    {
        $session = ConversationSession::where('session_id', $sessionId)->first();

        if (!$session) {
            return null;
        }

        return self::create([
            'conversation_session_id' => $session->id,
            'sender' => $sender,
            'message' => $message,
            'message_type' => $messageType,
            'metadata' => $metadata
        ]);
    }
}
