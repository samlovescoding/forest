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
    Schema::create('shows', function (Blueprint $table) {
      $table->id();

      $table->string('name');
      $table->string('slug')->unique();
      $table->text('overview')->nullable();
      $table->integer('episode_run_time')->nullable();
      $table->integer('number_of_seasons')->nullable();
      $table->integer('number_of_episodes')->nullable();

      $table->date('first_air_date')->nullable();
      $table->date('last_air_date')->nullable();

      $table->integer('vote_count')->nullable();
      $table->double('vote_average')->nullable();
      $table->double('popularity')->nullable();

      $table->string('backdrop_path')->nullable();
      $table->string('poster_path')->nullable();

      $table->string('tmdb_id')->nullable();
      $table->string('imdb_id')->nullable();
      $table->boolean('is_published')->default(false);
      $table->boolean('is_hidden')->default(false);

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('shows');
  }
};
