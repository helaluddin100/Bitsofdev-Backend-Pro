<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'answer',
        'visitor_ip',
        'visitor_session',
        'user_agent',
        'status',
        'is_answered',
        'is_converted',
        'qa_pair_id',
        'admin_notes'
    ];

    protected $casts = [
        'is_answered' => 'boolean',
        'is_converted' => 'boolean'
    ];

    public function qaPair()
    {
        return $this->belongsTo(QAPair::class, 'qa_pair_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAnswered($query)
    {
        return $query->where('status', 'answered');
    }

    public function scopeConverted($query)
    {
        return $query->where('status', 'converted');
    }
}
