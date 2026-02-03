<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Criar uma notificação
     */
    public function create(
        User $user,
        string $type,
        $notifiable,
        ?User $fromUser = null,
        ?string $message = null,
        ?array $data = null
    ): Notification {
        // Gerar mensagem padrão se não fornecida
        if (!$message) {
            $message = $this->generateMessage($type, $fromUser, $notifiable);
        }

        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->id,
            'from_user_id' => $fromUser?->id,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Notificar sobre comentário em carta
     */
    public function notifyCommentOnLetter($letter, User $commenter): void
    {
        // Não notificar se o comentário for do próprio autor
        if ($letter->user_id === $commenter->id) {
            return;
        }

        $this->create(
            $letter->user,
            'comment_letter',
            $letter,
            $commenter,
            null,
            ['letter_id' => $letter->id]
        );
    }

    /**
     * Notificar sobre comentário em meme
     */
    public function notifyCommentOnMeme($meme, User $commenter): void
    {
        // Não notificar se o comentário for do próprio autor
        if ($meme->user_id === $commenter->id) {
            return;
        }

        $this->create(
            $meme->user,
            'comment_meme',
            $meme,
            $commenter,
            null,
            ['meme_id' => $meme->id]
        );
    }

    /**
     * Notificar sobre like em carta
     */
    public function notifyLikeOnLetter($letter, User $liker): void
    {
        // Não notificar se o like for do próprio autor
        if ($letter->user_id === $liker->id) {
            return;
        }

        $this->create(
            $letter->user,
            'like_letter',
            $letter,
            $liker,
            null,
            ['letter_id' => $letter->id]
        );
    }

    /**
     * Gerar mensagem padrão baseada no tipo
     */
    private function generateMessage(string $type, ?User $fromUser, $notifiable): string
    {
        $userName = $fromUser?->name ?? 'Alguém';

        return match ($type) {
            'comment_letter' => "{$userName} comentou na sua carta",
            'comment_meme' => "{$userName} comentou no seu meme",
            'like_letter' => "{$userName} curtiu sua carta",
            default => "Você tem uma nova notificação",
        };
    }
}

