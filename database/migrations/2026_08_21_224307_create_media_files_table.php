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

            /*
     * Utilisateur ayant importé/ajouté le média.
     */
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
     * Type du média.
     */
            $table->foreignId('file_type_id')
                ->nullable()
                ->constrained('file_types')
                ->nullOnDelete();

            /*
     * Identifiant unique du média WordPress.
     */
            $table->unsignedBigInteger('wp_file_id')
                ->nullable()
                ->unique();

            /*
     * Informations du média.
     */
            $table->string('file_name');

            $table->string('file_slug');

            $table->text('file_url');

            /*
     * URL originale provenant de WordPress.
     */
            $table->text('wp_file')
                ->nullable();

            $table->text('caption')
                ->nullable();

            $table->softDeletes();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_files');
    }
};
