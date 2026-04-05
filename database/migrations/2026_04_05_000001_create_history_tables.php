<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('history_eras', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('nav_label');
            $table->string('slug')->unique();
            $table->string('tag_line')->nullable();
            $table->string('heading');
            $table->text('description');
            $table->string('bg_class')->default('vbg-1700');
            $table->string('caption_era');
            $table->string('caption_label');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('history_topics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('history_era_id');
            $table->string('title');
            $table->string('date_label');
            $table->text('summary');
            $table->string('image')->nullable();
            $table->string('bg_class')->default('vbg-1700');
            $table->string('caption_era');
            $table->string('caption_label');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('history_era_id')->references('id')->on('history_eras')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('history_topics');
        Schema::dropIfExists('history_eras');
    }
};
