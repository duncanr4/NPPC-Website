<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table('calendar_entries', function (Blueprint $table) {
            $table->uuid('prisoner_id')->nullable()->after('published');
        });
    }

    public function down(): void
    {
        Schema::table('calendar_entries', function (Blueprint $table) {
            $table->dropColumn('prisoner_id');
        });
    }
};
