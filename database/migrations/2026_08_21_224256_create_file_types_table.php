<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('file_types', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('name');

            /*
     * Type WordPress utilisé pour l'import.
     *
     * Exemples :
     * image
     * video
     * text
     * application
     * audio
     */
            $table->string('slug_wp')
                ->unique();

            $table->string('slug')
                ->unique();

            $table->softDeletes();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('file_types');
    }
};
