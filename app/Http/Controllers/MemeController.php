<?php

namespace App\Http\Controllers;

use App\Models\Meme;
use App\Models\MemeVote;
use App\Models\MemeComment;
use App\Models\HallOfFame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

class MemeController extends Controller
{
    public function index(): Response
    {
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        $userId = Auth::id();

        // Memes da semana (buscar modelos primeiro)
        $weekMemesQuery = Meme::with(['user.selectedBadge', 'votes.user.selectedBadge', 'comments.user.selectedBadge'])
            ->withCount('comments')
            ->whereBetween('meme_date', [$weekStart, $weekEnd])
            ->orderBy('total_votes', 'desc')
            ->get();

        // Determinar o ganhador da semana (o meme com mais votos da semana) ANTES do map
        $weekWinner = $weekMemesQuery->first();
        if ($weekWinner && $weekWinner->total_votes > 0) {
            // Remover is_winner de todos os memes da semana
            Meme::whereBetween('meme_date', [$weekStart, $weekEnd])
                ->update(['is_winner' => false]);
            
            // Marcar como vencedor se ainda não estiver marcado
            if (!$weekWinner->is_winner) {
                $weekWinner->update(['is_winner' => true]);
            }
        }

        // Agora mapear para arrays
        $weekMemes = $weekMemesQuery->map(function ($meme) use ($userId) {
                $userVote = $meme->getUserVote($userId);
                
                // Agrupar votos por emoji com informações dos usuários
                $votesByEmoji = [];
                $allVotes = $meme->votes()->with('user.selectedBadge')->get();
                foreach ($allVotes as $vote) {
                    if (!isset($votesByEmoji[$vote->emoji])) {
                        $votesByEmoji[$vote->emoji] = [
                            'count' => 0,
                            'users' => [],
                        ];
                    }
                    $votesByEmoji[$vote->emoji]['count']++;
                    $votesByEmoji[$vote->emoji]['users'][] = [
                        'id' => $vote->user->id,
                        'name' => $vote->user->name,
                        'avatar' => $vote->user->avatar ? Storage::url($vote->user->avatar) : null,
                        'selected_badge' => $vote->user->selectedBadge ? [
                            'id' => $vote->user->selectedBadge->id,
                            'name' => $vote->user->selectedBadge->name,
                            'icon' => $vote->user->selectedBadge->icon,
                            'color' => $vote->user->selectedBadge->color,
                        ] : null,
                    ];
                }
                
                return [
                    'id' => $meme->id,
                    'type' => $meme->type,
                    'media_path' => Storage::url($meme->media_path),
                    'caption' => $meme->caption,
                    'total_votes' => $meme->total_votes,
                    'is_winner' => $meme->is_winner,
                    'user_vote' => $userVote ? $userVote->emoji : null,
                    'votes_by_emoji' => $votesByEmoji,
                    'comments_count' => $meme->comments_count,
                    'comments' => $meme->comments->map(function ($comment) {
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
                    'user' => [
                        'id' => $meme->user->id,
                        'name' => $meme->user->name,
                        'avatar' => $meme->user->avatar ? Storage::url($meme->user->avatar) : null,
                        'selected_badge' => $meme->user->selectedBadge ? [
                            'id' => $meme->user->selectedBadge->id,
                            'name' => $meme->user->selectedBadge->name,
                            'icon' => $meme->user->selectedBadge->icon,
                            'color' => $meme->user->selectedBadge->color,
                        ] : null,
                    ],
                ];
            });

        // Hall da Fama (últimos 20)
        $hallOfFame = HallOfFame::with(['meme.user.selectedBadge'])
            ->orderBy('won_date', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($entry) {
                return [
                    'id' => $entry->id,
                    'meme' => [
                        'id' => $entry->meme->id,
                        'type' => $entry->meme->type,
                        'media_path' => Storage::url($entry->meme->media_path),
                        'caption' => $entry->meme->caption,
                        'user' => [
                            'id' => $entry->meme->user->id,
                            'name' => $entry->meme->user->name,
                            'avatar' => $entry->meme->user->avatar ? Storage::url($entry->meme->user->avatar) : null,
                            'selected_badge' => $entry->meme->user->selectedBadge ? [
                                'id' => $entry->meme->user->selectedBadge->id,
                                'name' => $entry->meme->user->selectedBadge->name,
                                'icon' => $entry->meme->user->selectedBadge->icon,
                                'color' => $entry->meme->user->selectedBadge->color,
                            ] : null,
                        ],
                    ],
                    'won_date' => $entry->won_date,
                    'votes_count' => $entry->votes_count,
                ];
            });

        return Inertia::render('memes/Index', [
            'week_memes' => $weekMemes,
            'hall_of_fame' => $hallOfFame,
            'week_start' => $weekStart->format('d/m/Y'),
            'week_end' => $weekEnd->format('d/m/Y'),
        ]);
    }

    public function store(Request $request)
    {
        // Verificar limites do PHP e usar o menor entre upload_max_filesize e post_max_size
        $uploadMax = $this->convertToBytes(ini_get('upload_max_filesize') ?: '2M');
        $postMax = $this->convertToBytes(ini_get('post_max_size') ?: '8M');
        $maxUploadKB = min($uploadMax, $postMax) / 1024; // Converter para KB
        
        $validated = $request->validate([
            'media' => 'required|file|mimes:jpeg,png,jpg,gif,webp,mp4,avi,mov,webm,mp3,wav,ogg,m4a|max:' . (int) $maxUploadKB,
            'caption' => 'nullable|string|max:500',
        ], [
            'media.required' => 'Por favor, selecione um arquivo para upload.',
            'media.file' => 'O arquivo enviado é inválido.',
            'media.mimes' => 'Formato de arquivo não suportado. Use: imagens (JPEG, PNG, JPG, GIF, WEBP), vídeos (MP4, AVI, MOV, WEBM) ou áudios (MP3, WAV, OGG, M4A).',
            'media.max' => 'O arquivo é muito grande. O limite atual do servidor é ' . round($maxUploadKB / 1024, 1) . 'MB. Para aumentar, edite o php.ini do Herd (veja HERD_PHP_CONFIG.md).',
            'caption.max' => 'A legenda não pode ter mais de 500 caracteres.',
        ]);

        $today = Carbon::today();
        
        if (!$request->hasFile('media')) {
            return redirect()->back()->withErrors(['media' => 'Nenhum arquivo foi enviado.']);
        }

        $file = $request->file('media');
        
        if (!$file || !$file->isValid()) {
            return redirect()->back()->withErrors(['media' => 'O arquivo enviado é inválido.']);
        }
        $mimeType = $file->getMimeType();
        
        // Determinar o tipo de mídia
        if (str_starts_with($mimeType, 'video/')) {
            $type = 'video';
        } elseif (str_starts_with($mimeType, 'audio/')) {
            $type = 'audio';
        } else {
            $type = 'image';
        }
        
        $path = $file->store('memes', 'public');

        // Remover is_winner de todos os memes da semana atual ao criar um novo
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        Meme::whereBetween('meme_date', [$weekStart, $weekEnd])
            ->update(['is_winner' => false]);

        $meme = Meme::create([
            'user_id' => Auth::id(),
            'type' => $type,
            'media_path' => $path,
            'caption' => $validated['caption'] ?? null,
            'meme_date' => $today,
        ]);

        // Registrar atividade
        $activityService = new \App\Services\ActivityService();
        $activityService->recordMemeCreated(Auth::user(), $meme);

        // Verificar badges
        $badgeService = new \App\Services\BadgeService();
        $badgeService->checkAndAwardBadges(Auth::user(), 'memes_count');

        return redirect()->back()->with('message', 'Meme postado com sucesso!');
    }

    public function vote(Request $request, Meme $meme)
    {
        $validated = $request->validate([
            'emoji' => 'required|string|max:10',
        ]);

        $userId = Auth::id();

        // Verificar se já votou
        $existingVote = $meme->getUserVote($userId);
        
        if ($existingVote) {
            // Atualizar voto existente
            if ($existingVote->emoji === $validated['emoji']) {
                // Se clicou no mesmo emoji, remove o voto
                $existingVote->delete();
                $meme->decrement('total_votes');
            } else {
                // Muda o emoji
                $existingVote->update(['emoji' => $validated['emoji']]);
            }
        } else {
            // Novo voto
            MemeVote::create([
                'meme_id' => $meme->id,
                'user_id' => $userId,
                'emoji' => $validated['emoji'],
            ]);
            $meme->increment('total_votes');
        }

        $meme->refresh();
        $meme->load(['votes.user.selectedBadge']);
        
        // Atualizar ganhador da semana
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        $weekMemes = Meme::whereBetween('meme_date', [$weekStart, $weekEnd])
            ->orderBy('total_votes', 'desc')
            ->get();
        
        // Remover is_winner de todos os memes da semana
        Meme::whereBetween('meme_date', [$weekStart, $weekEnd])
            ->update(['is_winner' => false]);
        
        // Marcar o meme com mais votos como ganhador
        $weekWinner = $weekMemes->first();
        if ($weekWinner && $weekWinner->total_votes > 0) {
            $weekWinner->update(['is_winner' => true]);
        }
        
        // Agrupar votos por emoji com informações dos usuários
        $votesByEmoji = [];
        $allVotes = $meme->votes;
        foreach ($allVotes as $vote) {
            if (!isset($votesByEmoji[$vote->emoji])) {
                $votesByEmoji[$vote->emoji] = [
                    'count' => 0,
                    'users' => [],
                ];
            }
            $votesByEmoji[$vote->emoji]['count']++;
            $votesByEmoji[$vote->emoji]['users'][] = [
                'id' => $vote->user->id,
                'name' => $vote->user->name,
                'avatar' => $vote->user->avatar ? Storage::url($vote->user->avatar) : null,
                'selected_badge' => $vote->user->selectedBadge ? [
                    'id' => $vote->user->selectedBadge->id,
                    'name' => $vote->user->selectedBadge->name,
                    'icon' => $vote->user->selectedBadge->icon,
                    'color' => $vote->user->selectedBadge->color,
                ] : null,
            ];
        }

        // Recarregar o meme para pegar o is_winner atualizado
        $meme->refresh();
        
        return response()->json([
            'total_votes' => $meme->total_votes,
            'user_vote' => $meme->getUserVote($userId)?->emoji,
            'votes_by_emoji' => $votesByEmoji,
            'is_winner' => $meme->is_winner,
        ]);
    }

    public function storeComment(Request $request, Meme $meme)
    {
        $validated = $request->validate([
            'content' => 'required|string|min:3|max:500',
        ]);

        $comment = MemeComment::create([
            'meme_id' => $meme->id,
            'user_id' => Auth::id(),
            'content' => $validated['content'],
        ]);

        // Criar notificação para o autor do meme
        $notificationService = new \App\Services\NotificationService();
        $notificationService->notifyCommentOnMeme($meme, Auth::user());

        // Se for uma requisição Inertia, redirecionar de volta
        if ($request->header('X-Inertia')) {
            return redirect()->back();
        }

        // Se for uma requisição AJAX tradicional, retornar JSON
        $comment->load('user.selectedBadge');

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
            'comments_count' => $meme->fresh()->comments()->count(),
        ]);
    }

    public function processDailyWinner()
    {
        $yesterday = Carbon::yesterday();
        
        // Buscar memes de ontem
        $yesterdayMemes = Meme::whereDate('meme_date', $yesterday)
            ->orderBy('total_votes', 'desc')
            ->first();

        if ($yesterdayMemes && $yesterdayMemes->total_votes > 0) {
            // Marcar como vencedor
            $yesterdayMemes->update([
                'is_winner' => true,
                'in_hall_of_fame' => true,
            ]);

            // Adicionar ao Hall da Fama
            HallOfFame::create([
                'meme_id' => $yesterdayMemes->id,
                'won_date' => $yesterday,
                'votes_count' => $yesterdayMemes->total_votes,
            ]);

            // Verificar badge de Hall da Fama
            $badgeService = new \App\Services\BadgeService();
            $badgeService->checkAndAwardBadges($yesterdayMemes->user, 'hall_of_fame');
        }

        return response()->json(['message' => 'Vencedor processado']);
    }

    /**
     * Converte valores como "8M", "25M" para bytes
     */
    private function convertToBytes(string $value): int
    {
        $value = trim($value);
        $last = strtolower($value[strlen($value) - 1]);
        $value = (int) $value;

        return match ($last) {
            'g' => $value * 1024 * 1024 * 1024,
            'm' => $value * 1024 * 1024,
            'k' => $value * 1024,
            default => $value,
        };
    }
}
