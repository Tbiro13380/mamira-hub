<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Badge;
use Illuminate\Http\RedirectResponse;

class BadgeController extends Controller
{
    public function index(): Response
    {
        $user = Auth::user();
        $userBadgeIds = $user->badges()->pluck('badge_id')->toArray();
        
        // Obter earned_at de forma segura usando query direta
        $userBadgesWithDates = DB::table('user_badges')
            ->where('user_id', $user->id)
            ->pluck('earned_at', 'badge_id')
            ->map(function ($earnedAt) {
                if (!$earnedAt) {
                    return null;
                }
                // Se já for string, retornar diretamente
                if (is_string($earnedAt)) {
                    return $earnedAt;
                }
                // Se for Carbon/DateTime, formatar
                if ($earnedAt instanceof \Carbon\Carbon || $earnedAt instanceof \DateTime) {
                    return $earnedAt->format('Y-m-d H:i:s');
                }
                // Caso contrário, converter para string
                return (string) $earnedAt;
            })
            ->toArray();

        $allBadges = Badge::orderBy('order')->get()->map(function ($badge) use ($userBadgeIds, $userBadgesWithDates) {
            $earned = in_array($badge->id, $userBadgeIds);
            $earnedAtString = $earned && isset($userBadgesWithDates[$badge->id]) 
                ? $userBadgesWithDates[$badge->id] 
                : null;

            return [
                'id' => $badge->id,
                'name' => $badge->name,
                'description' => $badge->description,
                'icon' => $badge->icon,
                'color' => $badge->color,
                'earned' => $earned,
                'earned_at' => $earnedAtString,
            ];
        });

        return Inertia::render('badges/Index', [
            'badges' => $allBadges,
            'selected_badge_id' => $user->selected_badge_id,
        ]);
    }

    public function updateSelected(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'badge_id' => 'nullable|exists:badges,id',
        ]);

        if ($validated['badge_id'] && !$user->hasBadge($validated['badge_id'])) {
            return redirect()->back()->withErrors(['badge_id' => 'Você não possui esta badge.']);
        }

        $user->selected_badge_id = $validated['badge_id'];
        $user->save();

        return redirect()->back()->with('message', 'Badge selecionada com sucesso!');
    }
}
