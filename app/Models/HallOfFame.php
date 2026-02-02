<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HallOfFame extends Model
{
    protected $table = 'hall_of_fame';

    protected $fillable = [
        'meme_id',
        'won_date',
        'votes_count',
    ];

    protected $casts = [
        'won_date' => 'date',
    ];

    public function meme(): BelongsTo
    {
        return $this->belongsTo(Meme::class);
    }
}
