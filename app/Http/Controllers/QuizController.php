<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizAnswer;
use App\Models\QuizMedal;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

class QuizController extends Controller
{
    public function index(): Response
    {
        $userId = Auth::id();
        $now = Carbon::now();

        // Quiz ativo da semana atual
        $activeQuiz = Quiz::with(['questions', 'creator.selectedBadge'])
            ->where('is_active', true)
            ->where('week_start_date', '<=', $now)
            ->where('week_end_date', '>=', $now)
            ->first();

        $activeQuizData = null;
        if ($activeQuiz) {
            $userAnswer = $activeQuiz->answers()->where('user_id', $userId)->first();
            $hasAnswered = $userAnswer !== null;
            
            $activeQuizData = [
                'id' => $activeQuiz->id,
                'title' => $activeQuiz->title,
                'description' => $activeQuiz->description,
                'week_start_date' => $activeQuiz->week_start_date,
                'week_end_date' => $activeQuiz->week_end_date,
                'has_answered' => $hasAnswered,
                'questions' => $hasAnswered ? $activeQuiz->questions->map(function ($q) use ($userAnswer) {
                    return [
                        'id' => $q->id,
                        'question' => $q->question,
                        'options' => $q->options,
                        'user_answer' => $userAnswer->answers[$q->id] ?? null,
                        'correct_answer' => $q->correct_answer,
                    ];
                }) : $activeQuiz->questions->map(function ($q) {
                    return [
                        'id' => $q->id,
                        'question' => $q->question,
                        'options' => $q->options,
                    ];
                }),
                'creator' => [
                    'id' => $activeQuiz->creator->id,
                    'name' => $activeQuiz->creator->name,
                    'avatar' => $activeQuiz->creator->avatar ? \Illuminate\Support\Facades\Storage::url($activeQuiz->creator->avatar) : null,
                    'selected_badge' => $activeQuiz->creator->selectedBadge ? [
                        'id' => $activeQuiz->creator->selectedBadge->id,
                        'name' => $activeQuiz->creator->selectedBadge->name,
                        'icon' => $activeQuiz->creator->selectedBadge->icon,
                        'color' => $activeQuiz->creator->selectedBadge->color,
                    ] : null,
                ],
            ];

            if ($hasAnswered) {
                $activeQuizData['user_result'] = [
                    'correct_count' => $userAnswer->correct_count,
                    'total_questions' => $userAnswer->total_questions,
                    'is_perfect' => $userAnswer->is_perfect,
                    'time_taken_seconds' => $userAnswer->time_taken_seconds,
                ];
            }
        }

        // Quizzes anteriores
        $pastQuizzes = Quiz::with(['creator.selectedBadge'])
            ->where('week_end_date', '<', $now)
            ->orderBy('week_end_date', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($quiz) use ($userId) {
                $userAnswer = $quiz->answers()->where('user_id', $userId)->first();
                return [
                    'id' => $quiz->id,
                    'title' => $quiz->title,
                    'week_start_date' => $quiz->week_start_date,
                    'week_end_date' => $quiz->week_end_date,
                    'has_answered' => $userAnswer !== null,
                    'user_result' => $userAnswer ? [
                        'correct_count' => $userAnswer->correct_count,
                        'total_questions' => $userAnswer->total_questions,
                        'is_perfect' => $userAnswer->is_perfect,
                    ] : null,
                    'creator' => [
                        'id' => $quiz->creator->id,
                        'name' => $quiz->creator->name,
                        'avatar' => $quiz->creator->avatar ? \Illuminate\Support\Facades\Storage::url($quiz->creator->avatar) : null,
                        'selected_badge' => $quiz->creator->selectedBadge ? [
                            'id' => $quiz->creator->selectedBadge->id,
                            'name' => $quiz->creator->selectedBadge->name,
                            'icon' => $quiz->creator->selectedBadge->icon,
                            'color' => $quiz->creator->selectedBadge->color,
                        ] : null,
                    ],
                ];
            });

        // Medalha ativa do usuário
        $activeMedal = QuizMedal::with('quiz')
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->where('expires_at', '>=', $now)
            ->first();

        return Inertia::render('quizzes/Index', [
            'active_quiz' => $activeQuizData,
            'past_quizzes' => $pastQuizzes,
            'active_medal' => $activeMedal ? [
                'quiz_title' => $activeMedal->quiz->title,
                'earned_date' => $activeMedal->earned_date,
                'expires_at' => $activeMedal->expires_at,
            ] : null,
        ]);
    }

    public function create()
    {
        return Inertia::render('quizzes/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'questions' => 'required|array|size:5',
            'questions.*.question' => 'required|string|max:500',
            'questions.*.correct_answer' => 'required|string|max:255',
            'questions.*.options' => 'required|array|size:4',
            'questions.*.options.*' => 'required|string|max:255',
        ]);

        $now = Carbon::now();
        $weekStart = $now->copy()->startOfWeek();
        $weekEnd = $now->copy()->endOfWeek();

        // Desativar quizzes anteriores
        Quiz::where('is_active', true)->update(['is_active' => false]);

        // Criar novo quiz
        $quiz = Quiz::create([
            'created_by' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'week_start_date' => $weekStart,
            'week_end_date' => $weekEnd,
            'is_active' => true,
        ]);

        // Criar perguntas
        foreach ($validated['questions'] as $index => $questionData) {
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => $questionData['question'],
                'correct_answer' => $questionData['correct_answer'],
                'options' => $questionData['options'],
                'order' => $index + 1,
            ]);
        }

        // Registrar atividade
        $activityService = new ActivityService();
        $activityService->recordQuizCreated(Auth::user(), $quiz);

        // Verificar badges
        $badgeService = new \App\Services\BadgeService();
        $badgeService->checkAndAwardBadges(Auth::user(), 'quizzes_created');

        return redirect()->route('quizzes.index')->with('message', 'Quiz criado com sucesso!');
    }

    public function submit(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'answers' => 'required|array',
            'time_taken_seconds' => 'required|integer|min:0',
        ]);

        $userId = Auth::id();

        // Verificar se já respondeu
        if ($quiz->hasAnsweredBy($userId)) {
            return redirect()->back()->withErrors(['answers' => 'Você já respondeu este quiz!']);
        }

        // Verificar se o quiz ainda está ativo
        $now = Carbon::now();
        if (!$quiz->is_active || $now < $quiz->week_start_date || $now > $quiz->week_end_date) {
            return redirect()->back()->withErrors(['answers' => 'Este quiz não está mais ativo!']);
        }

        // Calcular respostas corretas
        $questions = $quiz->questions;
        $correctCount = 0;
        $answers = [];

        foreach ($questions as $question) {
            $userAnswer = $validated['answers'][$question->id] ?? null;
            $answers[$question->id] = $userAnswer;
            
            if ($userAnswer === $question->correct_answer) {
                $correctCount++;
            }
        }

        $isPerfect = $correctCount === $questions->count();

        // Salvar resposta
        $quizAnswer = QuizAnswer::create([
            'quiz_id' => $quiz->id,
            'user_id' => $userId,
            'answers' => $answers,
            'correct_count' => $correctCount,
            'total_questions' => $questions->count(),
            'is_perfect' => $isPerfect,
            'time_taken_seconds' => $validated['time_taken_seconds'],
            'completed_at' => $now,
        ]);

        // Registrar atividade
        $activityService = new ActivityService();
        $activityService->recordQuizAnswered(Auth::user(), $quiz, $quizAnswer);

        // Verificar badges
        $badgeService = new \App\Services\BadgeService();
        $badgeService->checkAndAwardBadges(Auth::user(), 'quizzes_answered');

        // Se acertou tudo, verificar se é o primeiro e mais rápido - ganha medalha
        if ($isPerfect) {
            // Verificar badge de quiz perfeito
            $badgeService->checkAndAwardBadges(Auth::user(), 'quiz_perfect');
            
            // Registrar atividade de quiz perfeito (já registrado em recordQuizAnswered)
            // Buscar o primeiro que acertou tudo (mais rápido)
            $firstPerfect = QuizAnswer::where('quiz_id', $quiz->id)
                ->where('is_perfect', true)
                ->orderBy('completed_at', 'asc')
                ->orderBy('time_taken_seconds', 'asc')
                ->first();

            if ($firstPerfect && $firstPerfect->id === $quizAnswer->id) {
                // Primeiro a acertar tudo e mais rápido - ganha medalha
                $nextWeekEnd = $quiz->week_end_date->copy()->addWeek()->endOfWeek();
                
                // Desativar medalhas anteriores
                QuizMedal::where('user_id', $userId)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);

                QuizMedal::create([
                    'user_id' => $userId,
                    'quiz_id' => $quiz->id,
                    'earned_date' => $now,
                    'expires_at' => $nextWeekEnd,
                    'is_active' => true,
                ]);
            }
        }

        return redirect()->route('quizzes.index')->with('message', 'Quiz respondido com sucesso!');
    }
}
