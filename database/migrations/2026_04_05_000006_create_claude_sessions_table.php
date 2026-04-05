<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('claude_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('prompt');
            $table->string('status')->default('pending'); // pending, running, completed, failed
            $table->string('branch_name')->nullable();
            $table->string('worktree_path')->nullable();
            $table->longText('output')->nullable();
            $table->longText('diff')->nullable();
            $table->json('files_changed')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamp('merged_at')->nullable();
            $table->timestamp('discarded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claude_sessions');
    }
};
