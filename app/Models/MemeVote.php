<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemeVote extends Model
{
    protected $fillable = [
        'meme_id',
        'user_id',
        'emoji',
    ];

    public function meme(): BelongsTo
    {
        return $this->belongsTo(Meme::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
