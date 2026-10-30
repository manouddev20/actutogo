<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_media_file_id')
                ->constrained('type_media_files')
                ->restrictOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->nullOnDelete();
            $table->unsignedBigInteger('wp_file_id')
                ->nullable()
                ->unique();
            $table->string('name');
            $table->string('original_name')->nullable();
            $table->string('slug');
            $table->text('file_url');
            $table->text('path')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('extension')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->decimal('duration', 12, 3)->nullable();
            $table->string('title')->nullable();
            $table->text('caption')->nullable();
            $table->text('alt_text')->nullable();
            $table->longText('description')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_files');
    }
};