<?php

namespace App\Services;

use App\Models\User;
use App\Models\Badge;
use App\Models\Meme;
use App\Models\Photo;
use App\Models\Quiz;
use App\Models\HallOfFame;
use App\Models\QuizAnswer;
use Illuminate\Support\Facades\DB;

class BadgeService
{
    public function checkAndAwardBadges(User $user, string $action, array $context = []): void
    {
        $badges = Badge::where('condition_type', $action)->get();

        foreach ($badges as $badge) {
            if ($user->hasBadge($badge->id)) {
                continue;
            }

            $conditionMet = $this->checkCondition($user, $badge, $context);

            if ($conditionMet) {
                $this->awardBadge($user, $badge);
            }
        }
    }

    protected function checkCondition(User $user, Badge $badge, array $context = []): bool
    {
        return match ($badge->condition_type) {
            'letters_count' => $user->letters()->count() >= $badge->condition_value,
            'likes_received' => $this->getLikesReceived($user) >= $badge->condition_value,
            'comments_count' => $user->comments()->count() >= $badge->condition_value,
            'night_owl' => $this->checkNightOwl($context),
            'the_chosen_one' => $this->checkTheChosenOne($user),
            'memes_count' => $this->getMemesCount($user) >= $badge->condition_value,
            'hall_of_fame' => $this->checkHallOfFame($user) >= $badge->condition_value,
            'photos_count' => $this->getPhotosCount($user) >= $badge->condition_value,
            'quizzes_created' => $this->getQuizzesCreated($user) >= $badge->condition_value,
            'quizzes_answered' => $this->getQuizzesAnswered($user) >= $badge->condition_value,
            'quiz_perfect' => $this->checkQuizPerfect($user) >= $badge->condition_value,
            'leaderboard_top3' => $this->checkLeaderboardPosition($user, 3),
            'leaderboard_top1' => $this->checkLeaderboardPosition($user, 1),
            default => false,
        };
    }

    protected function checkNightOwl(array $context): bool
    {
        if (!isset($context['created_at'])) {
            return false;
        }

        $createdAt = is_string($context['created_at']) 
            ? \Carbon\Carbon::parse($context['created_at'])
            : $context['created_at'];

        $hour = (int) $createdAt->format('H');
        
        return $hour >= 0 && $hour < 5;
    }

    protected function checkTheChosenOne(User $user): bool
    {
        $email = strtolower($user->email);
        $name = strtolower($user->name);
        
        return str_contains($email, 'mamira') || 
               str_contains($name, 'mamira') ||
               str_contains($name, 'mamira-san');
    }

    protected function getLikesReceived(User $user): int
    {
        return DB::table('likes')
            ->join('letters', 'likes.letter_id', '=', 'letters.id')
            ->where('letters.user_id', $user->id)
            ->count();
    }

    protected function getMemesCount(User $user): int
    {
        return Meme::where('user_id', $user->id)->count();
    }

    protected function checkHallOfFame(User $user): int
    {
        return HallOfFame::whereHas('meme', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();
    }

    protected function getPhotosCount(User $user): int
    {
        return Photo::where('user_id', $user->id)->count();
    }

    protected function getQuizzesCreated(User $user): int
    {
        return Quiz::where('created_by', $user->id)->count();
    }

    protected function getQuizzesAnswered(User $user): int
    {
        return QuizAnswer::where('user_id', $user->id)->count();
    }

    protected function checkQuizPerfect(User $user): int
    {
        return QuizAnswer::where('user_id', $user->id)
            ->where('is_perfect', true)
            ->count();
    }

    protected function checkLeaderboardPosition(User $user, int $position): bool
    {
        $users = User::with('selectedBadge')
            ->withCount(['letters', 'comments', 'badges'])
            ->get()
            ->map(function ($u) {
                $likesReceived = DB::table('likes')
                    ->join('letters', 'likes.letter_id', '=', 'letters.id')
                    ->where('letters.user_id', $u->id)
                    ->count();

                $score = ($u->letters_count * 10) 
                    + ($likesReceived * 5) 
                    + ($u->comments_count * 2) 
                    + ($u->badges_count * 20);

                return [
                    'id' => $u->id,
                    'score' => $score,
                ];
            })
            ->sortByDesc('score')
            ->values()
            ->map(function ($u, $index) {
                return [
                    'id' => $u['id'],
                    'rank' => $index + 1,
                ];
            });

        $userRank = $users->firstWhere('id', $user->id);
        
        return $userRank && $userRank['rank'] <= $position;
    }

    protected function awardBadge(User $user, Badge $badge): void
    {
        $user->badges()->attach($badge->id, [
            'earned_at' => now(),
        ]);

        // Registrar atividade
        $activityService = new ActivityService();
        $activityService->recordBadgeEarned($user, $badge);
        
        // Verificar se alcançou o 1º lugar no leaderboard (badges dão muitos pontos)
        $activityService->checkAndRecordLeaderboardFirstPlace();
    }

    public function checkAllBadges(User $user): void
    {
        $allBadges = Badge::all();

        foreach ($allBadges as $badge) {
            if ($user->hasBadge($badge->id)) {
                continue;
            }

            if ($badge->condition_type === 'night_owl') {
                $hasNightLetter = $user->letters()
                    ->whereRaw("EXTRACT(HOUR FROM created_at) >= 0 AND EXTRACT(HOUR FROM created_at) < 5")
                    ->exists();
                
                if ($hasNightLetter) {
                    $this->awardBadge($user, $badge);
                }
                continue;
            }

            if ($badge->condition_type === 'the_chosen_one') {
                if ($this->checkTheChosenOne($user)) {
                    $this->awardBadge($user, $badge);
                }
                continue;
            }

            if (in_array($badge->condition_type, ['leaderboard_top3', 'leaderboard_top1'])) {
                if ($this->checkLeaderboardPosition($user, $badge->condition_type === 'leaderboard_top1' ? 1 : 3)) {
                    $this->awardBadge($user, $badge);
                }
                continue;
            }

            $conditionMet = $this->checkCondition($user, $badge);

            if ($conditionMet) {
                $this->awardBadge($user, $badge);
            }
        }
    }

    /**
     * Concede a badge "O Escolhido" ao usuário do Mamira-San
     */
    public function awardTheChosenOneBadge(User $user): bool
    {
        $badge = Badge::where('condition_type', 'the_chosen_one')->first();
        
        if (!$badge) {
            return false;
        }

        if ($user->hasBadge($badge->id)) {
            return false;
        }

        if ($this->checkTheChosenOne($user)) {
            $this->awardBadge($user, $badge);
            return true;
        }

        return false;
    }
}

