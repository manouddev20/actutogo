<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();

            /**
             * Utilisateur connecté.
             * Nullable pour les visiteurs.
             */
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();

            $table->string('email');

            $table->string('phone')->nullable();

            $table->string('subject');

            $table->string('title')->nullable();

            $table->longText('message');

            /**
             * 0 = non lu
             * 1 = lu
             * 2 = traité
             */
            $table->tinyInteger('status')->default(0);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};