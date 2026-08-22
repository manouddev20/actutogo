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

            $table->unsignedBigInteger('wp_publication_id')
                ->nullable()
                ->unique();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('author_id')
                ->nullable()
                ->constrained('authors')
                ->nullOnDelete();

            $table->foreignId('type_publication_id')
                ->nullable()
                ->constrained('type_publications')
                ->nullOnDelete();

            /*
     * Média de couverture.
     */
            $table->foreignId('cover_media_file_id')
                ->nullable()
                ->constrained('media_files')
                ->nullOnDelete();

            $table->string('title');

            $table->string('slug')
                ->unique();

            $table->string('title_truncate')
                ->nullable();

            $table->longText('content');

            $table->text('truncate_content')
                ->nullable();

            $table->longText('truncate_content_max')
                ->nullable();

            $table->boolean('status')
                ->default(false);

            $table->boolean('comment_status')
                ->default(true);

            $table->unsignedBigInteger('views_count')
                ->default(0);

            $table->unsignedBigInteger('likes_count')
                ->default(0);

            $table->unsignedBigInteger('shares_count')
                ->default(0);

            $table->unsignedBigInteger('comment_count')
                ->default(0);

            $table->string('source')
                ->nullable();

            $table->text('wp_link')
                ->nullable();

            $table->timestamp('date_publish')->nullable();
            
            $table->timestamp('date_modified')->nullable();

            $table->softDeletes();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publications');
    }
};
