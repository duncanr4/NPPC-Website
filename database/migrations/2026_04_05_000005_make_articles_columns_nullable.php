<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->text('image')->nullable()->change();
            $table->text('body')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->text('image')->nullable(false)->change();
            $table->text('body')->nullable(false)->change();
        });
    }
};
