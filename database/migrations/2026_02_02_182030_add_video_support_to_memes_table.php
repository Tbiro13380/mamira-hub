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
        Schema::table('memes', function (Blueprint $table) {
            $table->string('type')->default('image')->after('user_id'); // 'image' ou 'video'
            $table->renameColumn('image_path', 'media_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('memes', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->renameColumn('media_path', 'image_path');
        });
    }
};
