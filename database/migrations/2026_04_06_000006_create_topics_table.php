<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('topics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('slug')->unique();
            $table->uuid('parent_id')->nullable();
            $table->text('body')->nullable();
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('published')->default(true);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('topics')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('topics');
    }
};
