<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    public function index(): Response
    {
        $userId = Auth::id();

        $notifications = Notification::with(['fromUser.selectedBadge', 'notifiable'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'message' => $notification->message,
                    'read' => $notification->read,
                    'created_at' => $notification->created_at,
                    'from_user' => $notification->fromUser ? [
                        'id' => $notification->fromUser->id,
                        'name' => $notification->fromUser->name,
                        'avatar' => $notification->fromUser->avatar ? \Illuminate\Support\Facades\Storage::url($notification->fromUser->avatar) : null,
                        'selected_badge' => $notification->fromUser->selectedBadge ? [
                            'id' => $notification->fromUser->selectedBadge->id,
                            'name' => $notification->fromUser->selectedBadge->name,
                            'icon' => $notification->fromUser->selectedBadge->icon,
                            'color' => $notification->fromUser->selectedBadge->color,
                        ] : null,
                    ] : null,
                    'data' => $notification->data,
                    'notifiable_type' => $notification->notifiable_type,
                    'notifiable_id' => $notification->notifiable_id,
                ];
            });

        $unreadCount = Notification::where('user_id', $userId)
            ->where('read', false)
            ->count();

        return Inertia::render('notifications/Index', [
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markAsRead(Notification $notification): \Illuminate\Http\JsonResponse
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead(): \Illuminate\Http\JsonResponse
    {
        Notification::where('user_id', Auth::id())
            ->where('read', false)
            ->update(['read' => true]);

        return response()->json(['success' => true]);
    }

    public function getUnreadCount(): \Illuminate\Http\JsonResponse
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}
