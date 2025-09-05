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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->string('related_type'); // morph type
            $table->unsignedBigInteger('related_id'); // morph id
            $table->foreignId('file_id')->nullable()->constrained('files')->onDelete('cascade');
            $table->string('url')->nullable(); // external URL
            $table->string('name')->nullable(); // attachment name
            $table->text('description')->nullable(); // attachment description
            $table->string('type')->default('image'); // attachment type (image/document)
            $table->timestamps();
            
            // Indexes
            $table->index(['related_type', 'related_id']);
            $table->index(['file_id']);
            $table->index(['type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
