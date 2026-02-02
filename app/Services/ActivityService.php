<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\User;
use App\Models\Letter;
use App\Models\Badge;

class ActivityService
{
    public function recordLetterCreated(User $user, Letter $letter): void
    {
        Activity::create([
            'user_id' => $user->id,
            'type' => 'letter_created',
            'description' => "{$user->name} acabou de enviar uma carta pública",
            'icon' => '✉️',
            'metadata' => [
                'letter_id' => $letter->id,
            ],
        ]);
    }

    public function recordBadgeEarned(User $user, Badge $badge): void
    {
        Activity::create([
            'user_id' => $user->id,
            'type' => 'badge_earned',
            'description' => "{$user->name} ganhou a Badge '{$badge->name}' {$badge->icon}",
            'icon' => $badge->icon ?? '🏆',
            'metadata' => [
                'badge_id' => $badge->id,
                'badge_name' => $badge->name,
            ],
        ]);
    }

    public function recordLetterLiked(User $user, Letter $letter, int $likesCount): void
    {
        if ($likesCount > 0 && $likesCount % 5 === 0) {
            // Garantir que o usuário da carta está carregado
            if (!$letter->relationLoaded('user')) {
                $letter->load('user');
            }
            
            Activity::create([
                'user_id' => $letter->user_id,
                'type' => 'letter_liked',
                'description' => "A carta de {$letter->user->name} recebeu {$likesCount} curtidas",
                'icon' => '❤️',
                'metadata' => [
                    'letter_id' => $letter->id,
                    'likes_count' => $likesCount,
                ],
            ]);
        }
    }
}

