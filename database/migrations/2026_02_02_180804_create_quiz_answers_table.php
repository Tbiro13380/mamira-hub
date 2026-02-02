<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quiz_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('answers'); // JSON com as respostas do usuário {question_id: answer}
            $table->integer('correct_count')->default(0);
            $table->integer('total_questions')->default(0);
            $table->boolean('is_perfect')->default(false); // Se acertou todas
            $table->integer('time_taken_seconds')->nullable(); // Tempo em segundos
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['quiz_id', 'user_id']); // Um usuário só pode responder uma vez por quiz
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_answers');
    }
};
