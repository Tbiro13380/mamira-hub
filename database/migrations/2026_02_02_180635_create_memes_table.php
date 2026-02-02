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
        Schema::create('memes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->text('caption')->nullable();
            $table->date('meme_date'); // Data do meme (para agrupar por dia)
            $table->integer('total_votes')->default(0);
            $table->boolean('is_winner')->default(false); // Se ganhou no dia
            $table->boolean('in_hall_of_fame')->default(false); // Se está no hall da fama
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memes');
    }
};
