<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ChatController extends Controller
{
    public function index()
    {
        $messages = Message::with('user.selectedBadge')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->reverse()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'user_id' => $message->user_id,
                    'user_name' => $message->user->name,
                    'user_avatar' => $message->user->avatar ? Storage::url($message->user->avatar) : null,
                    'message' => $message->message,
                    'created_at' => $message->created_at->toIso8601String(),
                    'selected_badge' => $message->user->selectedBadge ? [
                        'id' => $message->user->selectedBadge->id,
                        'name' => $message->user->selectedBadge->name,
                        'icon' => $message->user->selectedBadge->icon,
                        'color' => $message->user->selectedBadge->color,
                    ] : null,
                ];
            });

        // Se for requisição AJAX/JSON, retornar JSON
        if (request()->wantsJson() || request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['messages' => $messages->values()]);
        }

        return Inertia::render('chat/Index', [
            'messages' => $messages,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|min:1|max:500',
        ]);

        $userId = Auth::id();

        // Prevenir mensagens duplicadas muito próximas (rate limiting básico)
        $lastMessage = Message::where('user_id', $userId)
            ->where('message', $validated['message'])
            ->where('created_at', '>=', now()->subSeconds(2))
            ->first();

        if ($lastMessage) {
            Log::warning('Tentativa de mensagem duplicada bloqueada:', [
                'user_id' => $userId,
                'message_id' => $lastMessage->id,
            ]);
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $lastMessage->id,
                    'user_id' => $lastMessage->user_id,
                    'user_name' => $lastMessage->user->name,
                    'user_avatar' => $lastMessage->user->avatar ? Storage::url($lastMessage->user->avatar) : null,
                    'message' => $lastMessage->message,
                    'created_at' => $lastMessage->created_at->toIso8601String(),
                    'selected_badge' => $lastMessage->user->selectedBadge ? [
                        'id' => $lastMessage->user->selectedBadge->id,
                        'name' => $lastMessage->user->selectedBadge->name,
                        'icon' => $lastMessage->user->selectedBadge->icon,
                        'color' => $lastMessage->user->selectedBadge->color,
                    ] : null,
                ],
            ]);
        }

        $message = Message::create([
            'user_id' => $userId,
            'message' => $validated['message'],
        ]);

        $message->load('user.selectedBadge');

        // Broadcast the message para todos os usuários conectados
        // A lógica de duplicação no frontend evita que a mensagem apareça duas vezes
        try {
            broadcast(new MessageSent($message));
            Log::info('Mensagem broadcastada:', [
                'message_id' => $message->id,
                'user_id' => $userId,
                'message' => substr($validated['message'], 0, 50),
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao fazer broadcast:', [
                'error' => $e->getMessage(),
                'message_id' => $message->id,
                'user_id' => $userId,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'user_id' => $message->user_id,
                'user_name' => $message->user->name,
                'user_avatar' => $message->user->avatar ? Storage::url($message->user->avatar) : null,
                'message' => $message->message,
                'created_at' => $message->created_at->toIso8601String(),
                'selected_badge' => $message->user->selectedBadge ? [
                    'id' => $message->user->selectedBadge->id,
                    'name' => $message->user->selectedBadge->name,
                    'icon' => $message->user->selectedBadge->icon,
                    'color' => $message->user->selectedBadge->color,
                ] : null,
            ],
        ]);
    }
}
