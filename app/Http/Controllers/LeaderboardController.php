<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\User;

class LeaderboardController extends Controller
{
    public function index(): Response
    {
        $currentUserId = Auth::id();

        // Calcular pontuação dos usuários baseado em:
        // - Cartas escritas (10 pontos cada)
        // - Likes recebidos (5 pontos cada)
        // - Comentários feitos (2 pontos cada)
        // - Badges conquistadas (20 pontos cada)

        $users = User::with('selectedBadge')
            ->withCount(['letters', 'comments', 'badges'])
            ->get()
            ->map(function ($user) {
                // Calcular likes recebidos
                $likesReceived = DB::table('likes')
                    ->join('letters', 'likes.letter_id', '=', 'letters.id')
                    ->where('letters.user_id', $user->id)
                    ->count();

                // Calcular pontuação
                $score = ($user->letters_count * 10) 
                    + ($likesReceived * 5) 
                    + ($user->comments_count * 2) 
                    + ($user->badges_count * 20);

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar' => $user->avatar ? \Illuminate\Support\Facades\Storage::url($user->avatar) : null,
                    'score' => $score,
                    'letters_count' => $user->letters_count,
                    'likes_received' => $likesReceived,
                    'comments_count' => $user->comments_count,
                    'badges_count' => $user->badges_count,
                    'selected_badge' => $user->selectedBadge ? [
                        'id' => $user->selectedBadge->id,
                        'name' => $user->selectedBadge->name,
                        'icon' => $user->selectedBadge->icon,
                        'color' => $user->selectedBadge->color,
                    ] : null,
                ];
            })
            ->sortByDesc('score')
            ->values()
            ->map(function ($user, $index) {
                return [
                    ...$user,
                    'rank' => $index + 1,
                ];
            })
            ->take(50); // Top 50

        return Inertia::render('leaderboard/Index', [
            'users' => $users,
            'current_user_id' => $currentUserId,
        ]);
    }
}
