<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizMedal extends Model
{
    protected $fillable = [
        'user_id',
        'quiz_id',
        'earned_date',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'earned_date' => 'date',
        'expires_at' => 'date',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }
}
