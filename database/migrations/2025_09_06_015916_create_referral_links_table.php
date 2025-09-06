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
        Schema::create('referral_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('code', 32)->unique();
            $table->string('name')->nullable(); // Название ссылки (например, "Instagram", "Telegram")
            $table->enum('type', ['social', 'messenger', 'offline', 'custom'])->default('custom');
            $table->boolean('is_active')->default(true);
            $table->integer('max_uses')->nullable(); // Максимальное количество использований
            $table->integer('current_uses')->default(0); // Текущее количество использований
            $table->timestamp('expires_at')->nullable(); // Срок действия ссылки
            $table->timestamps();
            
            $table->index(['user_id', 'is_active']);
            $table->index(['code', 'is_active']);
            $table->index(['expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_links');
    }
};