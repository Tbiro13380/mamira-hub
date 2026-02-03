<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'from_user_id',
        'message',
        'read',
        'data',
    ];

    protected $casts = [
        'read' => 'boolean',
        'data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function markAsRead(): void
    {
        $this->update(['read' => true]);
    }
}
