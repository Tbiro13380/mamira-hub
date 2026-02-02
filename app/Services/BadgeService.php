<?php

namespace App\Services;

use App\Models\User;
use App\Models\Badge;
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

    protected function awardBadge(User $user, Badge $badge): void
    {
        $user->badges()->attach($badge->id, [
            'earned_at' => now(),
        ]);

        // Registrar atividade
        $activityService = new ActivityService();
        $activityService->recordBadgeEarned($user, $badge);
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

