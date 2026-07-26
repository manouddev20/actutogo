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
        Schema::create('commentators', function (Blueprint $table) {
            $table->id();
            $table->string('nom_complet')->nullable();
            $table->string('username')->nullable();
            $table->string('email')->nullable();
            $table->string('slug');
            $table->dateTime('date_publish')->now();
            $table->unsignedBigInteger('user_id')->default(0);
            $table->unsignedBigInteger('wp_author_id')->default(0);
            $table->bigInteger('count_comments')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commentators');
    }
};
