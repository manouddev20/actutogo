<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('authors', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * Identifiant de l'auteur sur WordPress.
            */
            $table->unsignedBigInteger('wp_author_id')
                ->nullable()
                ->unique();

            $table->string('first_name');

            $table->string('last_name')
                ->nullable();

            $table->string('email')
                ->unique();

            $table->string('phone')
                ->nullable();

            $table->string('slug')
                ->unique();

            $table->longText('description')
                ->nullable();

            $table->text('photo')
                ->nullable();

            $table->boolean('status')
                ->default(true);

            $table->softDeletes();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
};
