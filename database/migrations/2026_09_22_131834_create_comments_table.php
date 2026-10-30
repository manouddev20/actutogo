<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('publication_id')
                ->constrained('publications')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->nullOnDelete();
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('comments')
                ->cascadeOnDelete();
            $table->string('nom_complet')->nullable();
            $table->string('email')->nullable();
            $table->text('content');
            $table->unsignedBigInteger('likes_count')->default(0);
            $table->unsignedBigInteger('reports_count')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['publication_id', 'status']);
            $table->index(['parent_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};