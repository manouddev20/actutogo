<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenoms');
            $table->string('nom_complet');
            $table->string('slug');
            $table->string('telephone')->nullable();
            $table->string('address')->nullable();
            $table->longText('description')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('authorName')->nullable();
            $table->integer('wp_author_id')->nullable();
            $table->integer('status')->default(1);
            $table->bigInteger('count_publications')->default(0);
            $table->dateTime('date_publish')->now(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
};
