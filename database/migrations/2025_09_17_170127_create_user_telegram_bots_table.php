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
        Schema::create('user_telegram_bots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('bot_name'); // 'main', 'admin', 'manager'
            $table->string('telegram_id');
            $table->json('bot_data')->nullable(); // Дополнительные данные бота
            $table->timestamps();
            
            $table->unique(['bot_name', 'telegram_id']);
            $table->index(['user_id', 'bot_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_telegram_bots');
    }
};