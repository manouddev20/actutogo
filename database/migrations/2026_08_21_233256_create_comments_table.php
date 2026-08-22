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
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('comments')
                ->cascadeOnDelete();

            $table->string('full_name')->nullable();

            $table->string('email')->nullable();

            $table->longText('content');

            $table->tinyInteger('status')->default(0);

            // ID du commentaire provenant de WordPress
            $table->unsignedBigInteger('wp_comment_id')
                ->nullable()
                ->unique();

            $table->softDeletes();

            $table->timestamps();

            $table->index('publication_id');
            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
