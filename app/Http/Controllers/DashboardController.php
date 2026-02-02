<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Letter;
use App\Models\Photo;
use App\Models\Tear;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $userId = Auth::id();
        $user = Auth::user();

        // Estatísticas globais
        $letters = Letter::select('content')->get();
        $totalWords = 0;
        foreach ($letters as $letter) {
            $words = str_word_count(strip_tags($letter->content), 0, 'áàâãéèêíìîóòôõúùûçÁÀÂÃÉÈÊÍÌÎÓÒÔÕÚÙÛÇ');
            $totalWords += $words;
        }
        $totalTears = Tear::count();
        $totalLetters = Letter::count();
        $totalUsers = User::count();

        // Estatísticas do usuário atual
        $userStats = [
            'letters_count' => $user->letters()->count(),
            'likes_received' => DB::table('likes')
                ->join('letters', 'likes.letter_id', '=', 'letters.id')
                ->where('letters.user_id', $userId)
                ->count(),
            'comments_count' => $user->comments()->count(),
            'badges_count' => $user->badges()->count(),
        ];

        // Últimas atividades (10 mais recentes)
        $recentActivities = Activity::with('user.selectedBadge')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'type' => $activity->type,
                    'description' => $activity->description,
                    'icon' => $activity->icon,
                    'metadata' => $activity->metadata,
                    'created_at' => $activity->created_at,
                    'user' => [
                        'id' => $activity->user->id,
                        'name' => $activity->user->name,
                        'avatar' => $activity->user->avatar ? Storage::url($activity->user->avatar) : null,
                        'selected_badge' => $activity->user->selectedBadge ? [
                            'id' => $activity->user->selectedBadge->id,
                            'name' => $activity->user->selectedBadge->name,
                            'icon' => $activity->user->selectedBadge->icon,
                            'color' => $activity->user->selectedBadge->color,
                        ] : null,
                    ],
                ];
            });

        // Últimas fotos (6 mais recentes)
        $recentPhotos = Photo::with('user.selectedBadge')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get()
            ->map(function ($photo) {
                return [
                    'id' => $photo->id,
                    'path' => Storage::url($photo->path),
                    'caption' => $photo->caption,
                    'created_at' => $photo->created_at,
                    'user' => [
                        'id' => $photo->user->id,
                        'name' => $photo->user->name,
                        'avatar' => $photo->user->avatar ? Storage::url($photo->user->avatar) : null,
                        'selected_badge' => $photo->user->selectedBadge ? [
                            'id' => $photo->user->selectedBadge->id,
                            'name' => $photo->user->selectedBadge->name,
                            'icon' => $photo->user->selectedBadge->icon,
                            'color' => $photo->user->selectedBadge->color,
                        ] : null,
                    ],
                ];
            });

        // Últimas cartas (5 mais recentes públicas)
        $recentLetters = Letter::with(['user.selectedBadge'])
            ->withCount(['likes', 'comments'])
            ->where('is_private', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($letter) use ($userId) {
                return [
                    'id' => $letter->id,
                    'title' => $letter->title,
                    'content' => mb_substr(strip_tags($letter->content), 0, 150) . '...',
                    'image' => $letter->image ? Storage::url($letter->image) : null,
                    'created_at' => $letter->created_at,
                    'user' => [
                        'id' => $letter->user->id,
                        'name' => $letter->user->name,
                        'avatar' => $letter->user->avatar ? Storage::url($letter->user->avatar) : null,
                        'selected_badge' => $letter->user->selectedBadge ? [
                            'id' => $letter->user->selectedBadge->id,
                            'name' => $letter->user->selectedBadge->name,
                            'icon' => $letter->user->selectedBadge->icon,
                            'color' => $letter->user->selectedBadge->color,
                        ] : null,
                    ],
                    'likes_count' => $letter->likes_count,
                    'comments_count' => $letter->comments_count,
                ];
            });

        // Badges recentes ganhas (últimas 5)
        $recentBadges = $user->badges()
            ->orderByPivot('earned_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($badge) {
                return [
                    'id' => $badge->id,
                    'name' => $badge->name,
                    'icon' => $badge->icon,
                    'color' => $badge->color,
                    'earned_at' => $badge->pivot->earned_at,
                ];
            });

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_words' => $totalWords,
                'total_tears' => $totalTears,
                'total_letters' => $totalLetters,
                'total_users' => $totalUsers,
            ],
            'user_stats' => $userStats,
            'recent_activities' => $recentActivities,
            'recent_photos' => $recentPhotos,
            'recent_letters' => $recentLetters,
            'recent_badges' => $recentBadges,
        ]);
    }
}
