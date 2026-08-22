<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comment_reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('comment_id')
                ->constrained('comments')
                ->cascadeOnDelete();

            /*
             * Utilisateur connecté.
             */
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * Visiteur non connecté.
             */
            $table->uuid('visitor_token')->nullable();

            /*
             * Raison du signalement.
             */
            $table->string('reason')->nullable();

            /*
             * Description complémentaire.
             */
            $table->longText('description')->nullable();

            $table->timestamps();

            $table->index('user_id');
            $table->index('visitor_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_reports');
    }
};