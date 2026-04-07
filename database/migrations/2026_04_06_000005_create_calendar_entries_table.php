<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('calendar_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('month'); // 1-12
            $table->integer('day');   // 1-31
            $table->integer('year');  // historical year
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('published')->default(true);
            $table->timestamps();

            $table->unique(['month', 'day']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_entries');
    }
};
