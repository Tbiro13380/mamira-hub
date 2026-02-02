<?php

namespace App\Http\Controllers;

use App\Models\Meme;
use App\Models\MemeVote;
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
        $userId = Auth::id();

        // Memes de hoje
        $todayMemes = Meme::with(['user.selectedBadge', 'votes'])
            ->whereDate('meme_date', $today)
            ->orderBy('total_votes', 'desc')
            ->get()
            ->map(function ($meme) use ($userId) {
                $userVote = $meme->getUserVote($userId);
                return [
                    'id' => $meme->id,
                    'type' => $meme->type,
                    'media_path' => Storage::url($meme->media_path),
                    'caption' => $meme->caption,
                    'total_votes' => $meme->total_votes,
                    'is_winner' => $meme->is_winner,
                    'user_vote' => $userVote ? $userVote->emoji : null,
                    'votes_by_emoji' => $meme->votes()->selectRaw('emoji, count(*) as count')
                        ->groupBy('emoji')
                        ->get()
                        ->pluck('count', 'emoji')
                        ->toArray(),
                    'user' => [
                        'id' => $meme->user->id,
                        'name' => $meme->user->name,
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
            'today_memes' => $todayMemes,
            'hall_of_fame' => $hallOfFame,
            'today_date' => $today->format('d/m/Y'),
        ]);
    }

    public function store(Request $request)
    {
        // Verificar limites do PHP e usar o menor entre upload_max_filesize e post_max_size
        $uploadMax = $this->convertToBytes(ini_get('upload_max_filesize') ?: '2M');
        $postMax = $this->convertToBytes(ini_get('post_max_size') ?: '8M');
        $maxUploadKB = min($uploadMax, $postMax) / 1024; // Converter para KB
        
        $validated = $request->validate([
            'media' => 'required|file|mimes:jpeg,png,jpg,gif,webp,mp4,avi,mov,webm|max:' . (int) $maxUploadKB,
            'caption' => 'nullable|string|max:500',
        ], [
            'media.max' => 'O arquivo é muito grande. O limite atual do servidor é ' . round($maxUploadKB / 1024, 1) . 'MB. Para aumentar, edite o php.ini do Herd (veja HERD_PHP_CONFIG.md).',
        ]);

        $today = Carbon::today();
        
        // Verificar se o usuário já postou hoje
        $existingMeme = Meme::where('user_id', Auth::id())
            ->whereDate('meme_date', $today)
            ->first();

        if ($existingMeme) {
            return redirect()->back()->withErrors(['media' => 'Você já postou um meme hoje!']);
        }

        $file = $request->file('media');
        $isVideo = in_array($file->getMimeType(), ['video/mp4', 'video/avi', 'video/quicktime', 'video/webm']);
        $type = $isVideo ? 'video' : 'image';
        $path = $file->store('memes', 'public');

        $meme = Meme::create([
            'user_id' => Auth::id(),
            'type' => $type,
            'media_path' => $path,
            'caption' => $validated['caption'] ?? null,
            'meme_date' => $today,
        ]);

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
        $votesByEmoji = $meme->votes()->selectRaw('emoji, count(*) as count')
            ->groupBy('emoji')
            ->get()
            ->pluck('count', 'emoji')
            ->toArray();

        return response()->json([
            'total_votes' => $meme->total_votes,
            'user_vote' => $meme->getUserVote($userId)?->emoji,
            'votes_by_emoji' => $votesByEmoji,
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
