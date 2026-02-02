<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Letter extends Model
{
    protected $fillable = [
        'user_id',
        'content',
        'is_private',
        'title',
        'image',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'asc');
    }

    public function isLikedBy(?int $userId): bool
    {
        if (!$userId) {
            return false;
        }

        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function canBeViewedBy(?int $userId): bool
    {
        // Cartas públicas podem ser vistas por todos
        if (!$this->is_private) {
            return true;
        }

        // Cartas privadas só podem ser vistas pelo autor
        return $this->user_id === $userId;
    }
}
