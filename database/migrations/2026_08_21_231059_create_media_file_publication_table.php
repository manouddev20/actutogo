<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('media_file_publication', function (Blueprint $table) {
            $table->foreignId('publication_id')
                ->constrained('publications')
                ->cascadeOnDelete();

            $table->foreignId('media_file_id')
                ->constrained('media_files')
                ->cascadeOnDelete();

            $table->unique([
                'publication_id',
                'media_file_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_file_publication');
    }
};