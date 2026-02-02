<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meme extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'media_path',
        'caption',
        'meme_date',
        'total_votes',
        'is_winner',
        'in_hall_of_fame',
    ];

    protected $casts = [
        'meme_date' => 'date',
        'is_winner' => 'boolean',
        'in_hall_of_fame' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(MemeVote::class);
    }

    public function hasVotedBy(int $userId): bool
    {
        return $this->votes()->where('user_id', $userId)->exists();
    }

    public function getUserVote(int $userId): ?MemeVote
    {
        return $this->votes()->where('user_id', $userId)->first();
    }
}
