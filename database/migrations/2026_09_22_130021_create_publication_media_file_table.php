<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publication_media_file', function (Blueprint $table) {
            $table->id();
            $table->foreignId('publication_id')
                ->constrained('publications')
                ->cascadeOnDelete();
            $table->foreignId('media_file_id')
                ->constrained('media_files')
                ->cascadeOnDelete();
            $table->string('role')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->unique(['publication_id', 'media_file_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publication_media_file');
    }
};