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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'comment_letter', 'comment_meme', 'like_letter', etc
            $table->morphs('notifiable'); // notifiable_type e notifiable_id (polimórfico)
            $table->foreignId('from_user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->text('message');
            $table->boolean('read')->default(false);
            $table->json('data')->nullable(); // Dados adicionais
            $table->timestamps();
            
            $table->index(['user_id', 'read']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
