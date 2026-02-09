<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('genres', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('tmdb_id')->unique();
            $table->timestamps();
        });

        Schema::create('film_genre', function (Blueprint $table) {
            $table->id();
            $table->foreignId('film_id')->constrained()->cascadeOnDelete();
            $table->foreignId('genre_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['film_id', 'genre_id']);
        });

        Schema::create('genre_show', function (Blueprint $table) {
            $table->id();
            $table->foreignId('show_id')->constrained()->cascadeOnDelete();
            $table->foreignId('genre_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['show_id', 'genre_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('genre_show');
        Schema::dropIfExists('film_genre');
        Schema::dropIfExists('genres');
    }
};
