<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->foreignId('type_publication_id')
                ->constrained('type_publications')
                ->restrictOnDelete();
            $table->foreignId('author_id')
                ->constrained('authors')
                ->restrictOnDelete();
            $table->foreignId('cover_media_file_id')
                ->nullable()
                ->constrained('media_files')
                ->restrictOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->unsignedBigInteger('wp_publication_id')->nullable()->unique();
            $table->string('title_truncated')->nullable();
            $table->longText('content');
            $table->text('summary')->nullable();
            $table->text('excerpt')->nullable();
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('likes_count')->default(0);
            $table->unsignedBigInteger('shares_count')->default(0);
            $table->unsignedBigInteger('comments_count')->default(0);
            $table->string('source')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publications');
    }
};