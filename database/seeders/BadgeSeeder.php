<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Badge;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            [
                'name' => 'Primeira Carta',
                'description' => 'Escreveu sua primeira carta para o Mamira-San',
                'icon' => '✉️',
                'color' => '#3b82f6',
                'condition_type' => 'letters_count',
                'condition_value' => 1,
                'order' => 1,
            ],
            [
                'name' => 'Escritor Dedicado',
                'description' => 'Escreveu 5 cartas',
                'icon' => '📝',
                'color' => '#10b981',
                'condition_type' => 'letters_count',
                'condition_value' => 5,
                'order' => 2,
            ],
            [
                'name' => 'Mestre das Cartas',
                'description' => 'Escreveu 10 cartas',
                'icon' => '📚',
                'color' => '#8b5cf6',
                'condition_type' => 'letters_count',
                'condition_value' => 10,
                'order' => 3,
            ],
            [
                'name' => 'Primeiro Like',
                'description' => 'Recebeu seu primeiro like',
                'icon' => '❤️',
                'color' => '#ef4444',
                'condition_type' => 'likes_received',
                'condition_value' => 1,
                'order' => 4,
            ],
            [
                'name' => 'Adorado',
                'description' => 'Recebeu 10 likes',
                'icon' => '💖',
                'color' => '#f59e0b',
                'condition_type' => 'likes_received',
                'condition_value' => 10,
                'order' => 5,
            ],
            [
                'name' => 'Primeiro Comentário',
                'description' => 'Fez seu primeiro comentário',
                'icon' => '💬',
                'color' => '#06b6d4',
                'condition_type' => 'comments_count',
                'condition_value' => 1,
                'order' => 6,
            ],
            [
                'name' => 'Comentarista Ativo',
                'description' => 'Fez 10 comentários',
                'icon' => '🗣️',
                'color' => '#14b8a6',
                'condition_type' => 'comments_count',
                'condition_value' => 10,
                'order' => 7,
            ],
            [
                'name' => 'Coruja',
                'description' => 'Escreveu uma carta de madrugada (entre 00h e 05h)',
                'icon' => '🦉',
                'color' => '#6366f1',
                'condition_type' => 'night_owl',
                'condition_value' => 1,
                'order' => 8,
            ],
            [
                'name' => 'O Escolhido',
                'description' => 'A única badge que só o Mamira-San tem no perfil dele',
                'icon' => '🥇',
                'color' => '#fbbf24',
                'condition_type' => 'the_chosen_one',
                'condition_value' => 1,
                'order' => 9,
            ],
            [
                'name' => 'Primeiro Meme',
                'description' => 'Postou seu primeiro meme',
                'icon' => '😂',
                'color' => '#ec4899',
                'condition_type' => 'memes_count',
                'condition_value' => 1,
                'order' => 10,
            ],
            [
                'name' => 'Memeiro',
                'description' => 'Postou 5 memes',
                'icon' => '🎭',
                'color' => '#a855f7',
                'condition_type' => 'memes_count',
                'condition_value' => 5,
                'order' => 11,
            ],
            [
                'name' => 'Rei dos Memes',
                'description' => 'Ganhou no Hall da Fama com um meme',
                'icon' => '👑',
                'color' => '#f59e0b',
                'condition_type' => 'hall_of_fame',
                'condition_value' => 1,
                'order' => 12,
            ],
            [
                'name' => 'Primeira Foto',
                'description' => 'Adicionou sua primeira foto ao mural',
                'icon' => '📸',
                'color' => '#06b6d4',
                'condition_type' => 'photos_count',
                'condition_value' => 1,
                'order' => 13,
            ],
            [
                'name' => 'Fotógrafo',
                'description' => 'Adicionou 5 fotos ao mural',
                'icon' => '📷',
                'color' => '#3b82f6',
                'condition_type' => 'photos_count',
                'condition_value' => 5,
                'order' => 14,
            ],
            [
                'name' => 'Criador de Quiz',
                'description' => 'Criou seu primeiro quiz',
                'icon' => '🧩',
                'color' => '#10b981',
                'condition_type' => 'quizzes_created',
                'condition_value' => 1,
                'order' => 15,
            ],
            [
                'name' => 'Respondedor',
                'description' => 'Respondeu seu primeiro quiz',
                'icon' => '✅',
                'color' => '#14b8a6',
                'condition_type' => 'quizzes_answered',
                'condition_value' => 1,
                'order' => 16,
            ],
            [
                'name' => 'Perfeccionista',
                'description' => 'Acertou todas as questões de um quiz',
                'icon' => '💯',
                'color' => '#fbbf24',
                'condition_type' => 'quiz_perfect',
                'condition_value' => 1,
                'order' => 17,
            ],
            [
                'name' => 'Top 3',
                'description' => 'Alcançou o top 3 do leaderboard',
                'icon' => '🥉',
                'color' => '#8b5cf6',
                'condition_type' => 'leaderboard_top3',
                'condition_value' => 1,
                'order' => 18,
            ],
            [
                'name' => 'Número 1',
                'description' => 'Alcançou o 1º lugar no leaderboard',
                'icon' => '🏆',
                'color' => '#f59e0b',
                'condition_type' => 'leaderboard_top1',
                'condition_value' => 1,
                'order' => 19,
            ],
            [
                'name' => 'Super Popular',
                'description' => 'Recebeu 50 likes',
                'icon' => '💝',
                'color' => '#ef4444',
                'condition_type' => 'likes_received',
                'condition_value' => 50,
                'order' => 20,
            ],
            [
                'name' => 'Ídolo',
                'description' => 'Recebeu 100 likes',
                'icon' => '🌟',
                'color' => '#f59e0b',
                'condition_type' => 'likes_received',
                'condition_value' => 100,
                'order' => 21,
            ],
            [
                'name' => 'Comentarista Expert',
                'description' => 'Fez 50 comentários',
                'icon' => '💭',
                'color' => '#06b6d4',
                'condition_type' => 'comments_count',
                'condition_value' => 50,
                'order' => 22,
            ],
        ];

        foreach ($badges as $badge) {
            Badge::firstOrCreate(
                ['name' => $badge['name']],
                $badge
            );
        }
    }
}
