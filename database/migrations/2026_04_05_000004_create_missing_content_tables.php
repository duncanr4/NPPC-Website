<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('authors')) {
            Schema::create('authors', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->text('about')->nullable();
                $table->string('avatar')->nullable();
            });
        }

        if (! Schema::hasTable('staff')) {
            Schema::create('staff', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('position')->nullable();
                $table->string('image')->nullable();
                $table->text('about')->nullable();
                $table->string('group')->nullable();
                $table->boolean('published')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('timelines')) {
            Schema::create('timelines', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('title');
                $table->integer('year');
                $table->text('text')->nullable();
                $table->string('image')->nullable();
            });
        }

        if (! Schema::hasTable('annual_reports')) {
            Schema::create('annual_reports', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('title');
                $table->string('file')->nullable();
                $table->string('image')->nullable();
            });
        }

        // Also add missing columns to articles table
        if (Schema::hasTable('articles') && ! Schema::hasColumn('articles', 'author_id')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->uuid('author_id')->nullable()->after('category_id');
                $table->date('published_at')->nullable()->after('body');
                $table->json('citations_json')->nullable()->after('published_at');
            });
        }

        // Add sort_order to faqs if missing
        if (Schema::hasTable('faqs') && ! Schema::hasColumn('faqs', 'sort_order')) {
            Schema::table('faqs', function (Blueprint $table) {
                $table->integer('sort_order')->default(0)->after('type');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('authors');
        Schema::dropIfExists('staff');
        Schema::dropIfExists('timelines');
        Schema::dropIfExists('annual_reports');
    }
};
