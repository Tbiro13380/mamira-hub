<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAnswer extends Model
{
    protected $fillable = [
        'quiz_id',
        'user_id',
        'answers',
        'correct_count',
        'total_questions',
        'is_perfect',
        'time_taken_seconds',
        'completed_at',
    ];

    protected $casts = [
        'answers' => 'array',
        'is_perfect' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
