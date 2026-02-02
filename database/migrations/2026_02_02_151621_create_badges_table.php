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
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('icon')->nullable(); // Nome do ícone ou emoji
            $table->string('color')->default('#3b82f6'); // Cor da badge
            $table->string('condition_type'); // Tipo de condição (letters_count, likes_received, etc)
            $table->integer('condition_value'); // Valor necessário para conquistar
            $table->integer('order')->default(0); // Ordem de exibição
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
