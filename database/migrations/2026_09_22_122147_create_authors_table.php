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
            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->unique()
                ->nullOnDelete();
            $table->string('nom');
            $table->string('prenom');
            $table->string('nom_complet');
            $table->string('nom_auteur');
            $table->string('slug')->unique();
            $table->string('telephone')->nullable();
            $table->string('adresse')->nullable();
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('wp_author_id')->nullable()->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
};