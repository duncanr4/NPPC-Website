<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('petitions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('body')->nullable();
            $table->string('image')->nullable();
            $table->string('recipients')->nullable();
            $table->text('suggested_subject')->nullable();
            $table->text('suggested_message')->nullable();
            $table->integer('signature_goal')->default(10000);
            $table->boolean('published')->default(true);
            $table->timestamps();
        });

        Schema::create('petition_signatures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('petition_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('phone')->nullable();
            $table->text('custom_message')->nullable();
            $table->timestamps();

            $table->foreign('petition_id')->references('id')->on('petitions')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('petition_signatures');
        Schema::dropIfExists('petitions');
    }
};
