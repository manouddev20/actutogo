<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comment_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')
                ->constrained('comments')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->nullOnDelete();
            $table->string('visitor_identifier')->nullable();
            $table->string('reason')->nullable();
            $table->text('description')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['comment_id', 'status']);
            $table->index(['comment_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_reports');
    }
};