<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publication_views', function (Blueprint $table) {
            $table->id();

            $table->foreignId('publication_id')
                ->constrained('publications')
                ->cascadeOnDelete();

            /*
             * Utilisateur connecté ou null.
             */
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * Visiteur anonyme.
             */
            $table->uuid('visitor_token')->nullable();

            $table->timestamps();

            $table->index('visitor_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publication_views');
    }
};