<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\User;
use App\Models\Letter;
use App\Models\Badge;
use App\Models\Meme;
use App\Models\Photo;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Services\BadgeService;

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

    public function recordMemeCreated(User $user, Meme $meme): void
    {
        Activity::create([
            'user_id' => $user->id,
            'type' => 'meme_created',
            'description' => "{$user->name} postou um novo meme!",
            'icon' => '🖼️',
            'metadata' => [
                'meme_id' => $meme->id,
            ],
        ]);
    }

    public function recordPhotoCreated(User $user, Photo $photo): void
    {
        Activity::create([
            'user_id' => $user->id,
            'type' => 'photo_created',
            'description' => "{$user->name} adicionou uma nova foto no mural",
            'icon' => '📸',
            'metadata' => [
                'photo_id' => $photo->id,
            ],
        ]);
    }

    public function recordQuizCreated(User $user, Quiz $quiz): void
    {
        Activity::create([
            'user_id' => $user->id,
            'type' => 'quiz_created',
            'description' => "{$user->name} criou um novo quiz semanal: '{$quiz->title}'",
            'icon' => '❓',
            'metadata' => [
                'quiz_id' => $quiz->id,
                'quiz_title' => $quiz->title,
            ],
        ]);
    }

    public function recordQuizAnswered(User $user, Quiz $quiz, QuizAnswer $answer): void
    {
        if ($answer->is_perfect) {
            Activity::create([
                'user_id' => $user->id,
                'type' => 'quiz_perfect',
                'description' => "{$user->name} acertou todas as perguntas do quiz '{$quiz->title}' em {$answer->time_taken_seconds}s! 🎯",
                'icon' => '🎯',
                'metadata' => [
                    'quiz_id' => $quiz->id,
                    'quiz_title' => $quiz->title,
                    'time_taken_seconds' => $answer->time_taken_seconds,
                    'is_perfect' => true,
                ],
            ]);
        } else {
            Activity::create([
                'user_id' => $user->id,
                'type' => 'quiz_answered',
                'description' => "{$user->name} respondeu o quiz '{$quiz->title}' ({$answer->correct_count}/{$answer->total_questions} corretas)",
                'icon' => '📝',
                'metadata' => [
                    'quiz_id' => $quiz->id,
                    'quiz_title' => $quiz->title,
                    'correct_count' => $answer->correct_count,
                    'total_questions' => $answer->total_questions,
                ],
            ]);
        }
    }

    public function recordLeaderboardFirstPlace(User $user): void
    {
        // Verificar se já registrou essa atividade recentemente (últimas 24h)
        $recentActivity = Activity::where('user_id', $user->id)
            ->where('type', 'leaderboard_first')
            ->where('created_at', '>', now()->subDay())
            ->exists();

        if (!$recentActivity) {
            Activity::create([
                'user_id' => $user->id,
                'type' => 'leaderboard_first',
                'description' => "🏆 {$user->name} alcançou o 1º lugar no leaderboard!",
                'icon' => '👑',
                'metadata' => [
                    'rank' => 1,
                ],
            ]);

            // Verificar badges de leaderboard
            $badgeService = new BadgeService();
            $badgeService->checkAndAwardBadges($user, 'leaderboard_top1');
            $badgeService->checkAndAwardBadges($user, 'leaderboard_top3');
        }
    }

    public function checkAndRecordLeaderboardFirstPlace(): void
    {
        // Calcular pontuação de todos os usuários
        $users = \App\Models\User::withCount(['letters', 'comments', 'badges'])
            ->get()
            ->map(function ($user) {
                $likesReceived = \Illuminate\Support\Facades\DB::table('likes')
                    ->join('letters', 'likes.letter_id', '=', 'letters.id')
                    ->where('letters.user_id', $user->id)
                    ->count();

                $score = ($user->letters_count * 10) 
                    + ($likesReceived * 5) 
                    + ($user->comments_count * 2) 
                    + ($user->badges_count * 20);

                return [
                    'user' => $user,
                    'score' => $score,
                ];
            })
            ->sortByDesc('score')
            ->values();

        // Verificar se o primeiro lugar mudou
        if ($users->isNotEmpty()) {
            $firstPlaceUser = $users->first()['user'];
            $this->recordLeaderboardFirstPlace($firstPlaceUser);
        }
    }
}

