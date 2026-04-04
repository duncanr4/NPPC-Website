<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('prisoner_cases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('prisoner_id');
            $table->uuid('institution_id')->nullable();
            $table->text('charges')->nullable();
            $table->date('arrest_date')->nullable();
            $table->string('indicted')->nullable();
            $table->string('convicted')->nullable();
            $table->string('plead')->nullable();
            $table->date('sentenced_date')->nullable();
            $table->date('incarceration_date')->nullable();
            $table->date('release_date')->nullable();
            $table->date('death_in_custody_date')->nullable();
            $table->date('in_exile_since')->nullable();
            $table->date('end_of_exile')->nullable();
            $table->string('prosecutor')->nullable();
            $table->string('judge')->nullable();
            $table->text('sentence')->nullable();
            $table->integer('imprisoned_for_days')->nullable();
            $table->integer('in_exile_for_days')->nullable();
            $table->timestamps();

            $table->foreign('prisoner_id')->references('id')->on('prisoners')->onDelete('cascade');
            $table->foreign('institution_id')->references('id')->on('institutions')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prisoner_cases');
    }
};
