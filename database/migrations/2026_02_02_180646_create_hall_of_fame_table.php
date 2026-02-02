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
        Schema::create('hall_of_fame', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meme_id')->constrained()->onDelete('cascade');
            $table->date('won_date'); // Data em que ganhou
            $table->integer('votes_count'); // Número de votos que recebeu
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hall_of_fame');
    }
};
