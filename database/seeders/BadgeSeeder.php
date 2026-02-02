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
        ];

        foreach ($badges as $badge) {
            Badge::firstOrCreate(
                ['name' => $badge['name']],
                $badge
            );
        }
    }
}
