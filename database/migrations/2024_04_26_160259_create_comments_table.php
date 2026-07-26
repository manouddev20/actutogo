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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->longText('content');
            $table->unsignedBigInteger('publication_id');
            $table->integer('status')->default(1);
            $table->unsignedBigInteger('commentator_id');
            $table->unsignedBigInteger('wp_comment_id');
            $table->integer('count_signal')->default(0);
            $table->integer('content_answer_status')->default(0);
            $table->integer('content_answer_count')->default(0);
            $table->integer('is_answer')->default(0);
            $table->dateTime('date_publish')->now();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
