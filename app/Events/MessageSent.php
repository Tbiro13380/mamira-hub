<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('chat'),
        ];
    }

    public function broadcastWith(): array
    {
        $this->message->load('user.selectedBadge');
        
        return [
            'id' => $this->message->id,
            'user_id' => $this->message->user_id,
            'user_name' => $this->message->user->name,
            'user_avatar' => $this->message->user->avatar ? Storage::url($this->message->user->avatar) : null,
            'message' => $this->message->message,
            'created_at' => $this->message->created_at->toIso8601String(),
            'selected_badge' => $this->message->user->selectedBadge ? [
                'id' => $this->message->user->selectedBadge->id,
                'name' => $this->message->user->selectedBadge->name,
                'icon' => $this->message->user->selectedBadge->icon,
                'color' => $this->message->user->selectedBadge->color,
            ] : null,
        ];
    }

    public function broadcastAs(): string
    {
        return 'MessageSent';
    }
}
