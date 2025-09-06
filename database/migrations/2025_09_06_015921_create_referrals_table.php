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
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_link_id')->constrained()->onDelete('cascade');
            $table->foreignId('referrer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('referred_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('visitor_cookie_id', 64)->nullable();
            $table->string('visitor_fingerprint', 64)->nullable();
            $table->string('visitor_ip', 45);
            $table->text('user_agent')->nullable();
            $table->text('redirect_url')->nullable();
            $table->enum('status', ['pending', 'completed', 'expired'])->default('pending');
            $table->timestamp('expires_at'); // Когда истекает возможность регистрации
            $table->json('metadata')->nullable(); // Дополнительные данные
            $table->timestamps();
            
            $table->index(['visitor_cookie_id']);
            $table->index(['visitor_fingerprint']);
            $table->index(['referral_link_id', 'status']);
            $table->index(['referrer_id', 'status']);
            $table->index(['expires_at']);
            $table->index(['status', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};