<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityController extends Controller
{
    public function index(): Response
    {
        $activities = Activity::with('user.selectedBadge')
            ->orderBy('created_at', 'desc')
            ->limit(50)
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
                        'avatar' => $activity->user->avatar ? \Illuminate\Support\Facades\Storage::url($activity->user->avatar) : null,
                        'selected_badge' => $activity->user->selectedBadge ? [
                            'id' => $activity->user->selectedBadge->id,
                            'name' => $activity->user->selectedBadge->name,
                            'icon' => $activity->user->selectedBadge->icon,
                            'color' => $activity->user->selectedBadge->color,
                        ] : null,
                    ],
                ];
            });

        return Inertia::render('activities/Index', [
            'activities' => $activities,
        ]);
    }
}
