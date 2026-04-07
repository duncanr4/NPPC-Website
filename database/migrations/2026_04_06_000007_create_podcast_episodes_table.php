<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('podcast_episodes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('show_name')->nullable();
            $table->text('description')->nullable();
            $table->string('audio_url')->nullable();
            $table->text('embed_code')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('duration')->nullable();
            $table->integer('episode_number')->nullable();
            $table->uuid('prisoner_id')->nullable();
            $table->boolean('published')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('prisoner_id')->references('id')->on('prisoners')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('podcast_episodes');
    }
};
