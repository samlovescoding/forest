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
      $table->string('overview');
      $table->string('episode_run_time'); // comma separated list of integers
      $table->integer('number_of_seasons');
      $table->integer('number_of_episodes');

      $table->date('first_air_date');
      $table->date('last_air_date');

      $table->integer('vote_count');
      $table->double('vote_average');
      $table->double('popularity');

      $table->string('backdrop_path')->nullable();
      $table->string('poster_path')->nullable();

      $table->string('tmdb_id');
      $table->string('imdb_id');
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
