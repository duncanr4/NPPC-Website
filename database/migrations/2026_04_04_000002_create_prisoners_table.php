<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('prisoners', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->integer('sort_order')->default(0);
            $table->string('photo')->nullable();
            $table->text('description')->nullable();
            $table->integer('years_in_prison')->nullable();
            $table->string('state')->nullable();
            $table->text('address')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('aka')->nullable();
            $table->string('race')->nullable();
            $table->string('gender')->nullable();
            $table->date('birthdate')->nullable();
            $table->date('death_date')->nullable();
            $table->integer('age')->nullable();
            $table->json('ideologies')->nullable();
            $table->string('era')->nullable();
            $table->json('affiliation')->nullable();
            $table->boolean('in_custody')->default(false);
            $table->boolean('released')->default(false);
            $table->boolean('in_exile')->default(false);
            $table->boolean('currently_in_exile')->default(false);
            $table->boolean('imprisoned_or_exiled')->default(false);
            $table->string('website')->nullable();
            $table->string('twitter')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('inmate_number')->nullable();
            $table->boolean('awaiting_trial')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prisoners');
    }
};
