<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publication_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('publication_id')
                ->constrained('publications')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->nullOnDelete();
            $table->string('visitor_identifier')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();

            $table->index(['publication_id', 'user_id']);
            $table->index(['publication_id', 'visitor_identifier']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publication_likes');
    }
};