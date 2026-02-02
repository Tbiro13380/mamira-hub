<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Letter;
use App\Models\User;
use App\Models\Like;
use App\Models\Comment;
use App\Services\BadgeService;
use App\Services\ActivityService;

class LetterController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $letters = Letter::with(['user.selectedBadge', 'likes', 'comments.user.selectedBadge'])
            ->withCount(['likes', 'comments'])
            ->where(function ($query) use ($userId) {
                $query->where('is_private', false)
                    ->orWhere(function ($q) use ($userId) {
                        $q->where('is_private', true)
                            ->where('user_id', $userId);
                    });
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($letter) use ($userId) {
                if (!$letter->canBeViewedBy($userId)) {
                    return null;
                }

                return [
                    'id' => $letter->id,
                    'title' => $letter->title,
                    'content' => $letter->content,
                    'image' => $letter->image ? Storage::url($letter->image) : null,
                    'is_private' => $letter->is_private,
                    'created_at' => $letter->created_at,
                    'user_id' => $letter->user_id,
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
                    'is_liked' => $letter->isLikedBy($userId),
                    'comments_count' => $letter->comments_count,
                    'comments' => $letter->comments->map(function ($comment) {
                        return [
                            'id' => $comment->id,
                            'content' => $comment->content,
                            'created_at' => $comment->created_at,
                            'user' => [
                                'id' => $comment->user->id,
                                'name' => $comment->user->name,
                                'avatar' => $comment->user->avatar ? Storage::url($comment->user->avatar) : null,
                                'selected_badge' => $comment->user->selectedBadge ? [
                                    'id' => $comment->user->selectedBadge->id,
                                    'name' => $comment->user->selectedBadge->name,
                                    'icon' => $comment->user->selectedBadge->icon,
                                    'color' => $comment->user->selectedBadge->color,
                                ] : null,
                            ],
                        ];
                    }),
                ];
            })
            ->filter();

        return Inertia::render('letters/Index', [
            'letters' => $letters,
        ]);
    }

    public function create()
    {
        return Inertia::render('letters/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'   => 'nullable|string|max:255',
            'content' => 'required|string|min:10',
            'is_private' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('letters', 'public');
        }

        $user = User::find(Auth::user()->id);
        $letter = $user->letters()->create($validated);

        // Registrar atividade apenas se a carta for pública
        if (!$letter->is_private) {
            $activityService = new ActivityService();
            $activityService->recordLetterCreated($user, $letter);
        }

        // Verificar e conceder badges
        $badgeService = new BadgeService();
        $badgeService->checkAndAwardBadges($user, 'letters_count');
        
        // Verificar se alcançou o 1º lugar no leaderboard
        $activityService->checkAndRecordLeaderboardFirstPlace();
        
        // Verificar badge Coruja (carta de madrugada)
        $badgeService->checkAndAwardBadges($user, 'night_owl', [
            'created_at' => $letter->created_at,
        ]);

        return redirect()->route('letters.index')->with('message', 'Sua carta para o Mamira-San foi enviada com sucesso!');
    }

    public function toggleLike(Request $request, Letter $letter)
    {
        $userId = Auth::id();

        if ($letter->is_private && $letter->user_id !== $userId) {
            abort(403);
        }

        $like = Like::where('user_id', $userId)
            ->where('letter_id', $letter->id)
            ->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            Like::create([
                'user_id' => $userId,
                'letter_id' => $letter->id,
            ]);
            $liked = true;

            // Verificar badges do autor da carta
            $badgeService = new BadgeService();
            $badgeService->checkAndAwardBadges($letter->user, 'likes_received');
        }

        $letter->refresh();
        $letter->loadCount('likes');
        $letter->load('user');

        // Registrar atividade quando a carta recebe múltiplos de 5 likes
        if ($liked) {
            $activityService = new ActivityService();
            $activityService->recordLetterLiked(Auth::user(), $letter, $letter->likes_count);
            // Verificar se o autor alcançou o 1º lugar
            $activityService->checkAndRecordLeaderboardFirstPlace();
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => $letter->likes_count,
        ]);
    }

    public function storeComment(Request $request, Letter $letter)
    {
        $userId = Auth::id();

        if (!$letter->canBeViewedBy($userId)) {
            abort(403, 'Você não tem permissão para comentar nesta carta.');
        }

        $validated = $request->validate([
            'content' => 'required|string|min:3|max:1000',
        ]);

        $user = Auth::user();
        $comment = Comment::create([
            'user_id' => $userId,
            'letter_id' => $letter->id,
            'content' => $validated['content'],
        ]);

        // Verificar badges do usuário que comentou
        $badgeService = new BadgeService();
        $badgeService->checkAndAwardBadges($user, 'comments_count');
        
        // Verificar se alcançou o 1º lugar no leaderboard
        $activityService = new ActivityService();
        $activityService->checkAndRecordLeaderboardFirstPlace();

        $comment->load('user');

        $comment->user->load('selectedBadge');

        return response()->json([
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'created_at' => $comment->created_at,
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                    'avatar' => $comment->user->avatar ? Storage::url($comment->user->avatar) : null,
                    'selected_badge' => $comment->user->selectedBadge ? [
                        'id' => $comment->user->selectedBadge->id,
                        'name' => $comment->user->selectedBadge->name,
                        'icon' => $comment->user->selectedBadge->icon,
                        'color' => $comment->user->selectedBadge->color,
                    ] : null,
                ],
            ],
            'comments_count' => $letter->fresh()->comments()->count(),
        ]);
    }

    public function destroy(Letter $letter)
    {
        if ($letter->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para excluir esta carta.');
        }

        if ($letter->image) {
            Storage::disk('public')->delete($letter->image);
        }

        $letter->delete();

        return redirect()->back()->with('message', 'Carta excluída com sucesso!');
    }
}
