<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comment_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')
                ->constrained('comments')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->nullOnDelete();
            $table->string('visitor_identifier')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();

            $table->index(['comment_id', 'user_id']);
            $table->index(['comment_id', 'visitor_identifier']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_likes');
    }
};