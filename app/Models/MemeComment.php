<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemeComment extends Model
{
    protected $fillable = [
        'meme_id',
        'user_id',
        'content',
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
