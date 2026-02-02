<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PhotoController extends Controller
{
    public function index(): Response
    {
        $photos = Photo::with('user.selectedBadge')
            ->orderBy('created_at', 'desc')
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

        return Inertia::render('photos/Index', [
            'photos' => $photos,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'caption' => 'nullable|string|max:500',
        ]);

        $path = $request->file('photo')->store('photos', 'public');

        $photo = Photo::create([
            'user_id' => Auth::id(),
            'path' => $path,
            'caption' => $validated['caption'] ?? null,
        ]);

        // Registrar atividade
        $activityService = new \App\Services\ActivityService();
        $activityService->recordPhotoCreated(Auth::user(), $photo);

        // Verificar badges
        $badgeService = new \App\Services\BadgeService();
        $badgeService->checkAndAwardBadges(Auth::user(), 'photos_count');

        return redirect()->back()->with('message', 'Foto enviada com sucesso!');
    }

    public function destroy(Photo $photo)
    {
        if ($photo->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para excluir esta foto.');
        }

        Storage::disk('public')->delete($photo->path);
        $photo->delete();

        return redirect()->back()->with('message', 'Foto excluída com sucesso!');
    }
}
