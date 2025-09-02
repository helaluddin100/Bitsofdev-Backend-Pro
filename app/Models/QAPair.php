<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QAPair extends Model
{
    use HasFactory;

    protected $table = 'qa_pairs';

    protected $fillable = [
        'question',
        'answer_1',
        'answer_2',
        'answer_3',
        'answer_4',
        'answer_5',
        'category',
        'is_active',
        'usage_count'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'usage_count' => 'integer'
    ];

    public function getAnswersAttribute()
    {
        return collect([
            $this->answer_1,
            $this->answer_2,
            $this->answer_3,
            $this->answer_4,
            $this->answer_5
        ])->filter()->values()->toArray();
    }

    public function incrementUsage()
    {
        $this->increment('usage_count');
    }
}
