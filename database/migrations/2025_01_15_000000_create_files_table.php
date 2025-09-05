<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('original_name');
            $table->string('mime_type');
            $table->unsignedBigInteger('size');
            $table->string('extension');
            $table->string('disk')->default('local');
            $table->string('path');
            $table->string('key', 8)->unique()->nullable();
            $table->boolean('is_public')->default(false);
            $table->json('metadata')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};