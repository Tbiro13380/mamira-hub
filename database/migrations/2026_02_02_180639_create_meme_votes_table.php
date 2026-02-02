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
        Schema::create('meme_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meme_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('emoji'); // Emoji usado no voto (😄, 😂, 🔥, etc)
            $table->timestamps();
            $table->unique(['meme_id', 'user_id']); // Um usuário só pode votar uma vez por meme
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meme_votes');
    }
};
