<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use App\Models\Tear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class StatsController extends Controller
{
    public function index(): Response
    {
        // Total de palavras escritas (contando palavras em todas as cartas)
        $letters = Letter::select('content')->get();
        $totalWords = 0;
        foreach ($letters as $letter) {
            $words = str_word_count(strip_tags($letter->content), 0, 'áàâãéèêíìîóòôõúùûçÁÀÂÃÉÈÊÍÌÎÓÒÔÕÚÙÛÇ');
            $totalWords += $words;
        }

        // Total de lágrimas derramadas
        $totalTears = Tear::count();

        return Inertia::render('stats/Index', [
            'total_words' => $totalWords,
            'total_tears' => $totalTears,
        ]);
    }

    public function addTear(Request $request)
    {
        Tear::create([
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
        ]);

        $totalTears = Tear::count();

        return response()->json([
            'total_tears' => $totalTears,
        ]);
    }
}
