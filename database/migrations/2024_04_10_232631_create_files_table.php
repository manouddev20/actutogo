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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->longText('file_name')->nullable();
            $table->longText('file_slug')->nullable();
            $table->longText('file_url')->nullable();
            $table->longText('caption')->nullable();
            $table->longText('legende')->nullable();
            $table->string('date_name');
            $table->string('date_name_default')->default('ALL');
            $table->integer('user_id')->default(1);
            $table->longText('wp_file')->nullable();
            $table->string('type_file_id')->nullable();
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
        Schema::dropIfExists('files');
    }
};
