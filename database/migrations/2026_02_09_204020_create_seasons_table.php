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
    Schema::create('seasons', function (Blueprint $table) {
      $table->id();

      $table->foreignId('show_id')->constrained()->cascadeOnDelete();

      $table->string('name');
      $table->string('slug');
      $table->text('overview')->nullable();
      $table->integer('season_number');
      $table->integer('episode_count')->nullable();
      $table->date('air_date')->nullable();

      $table->double('vote_average')->nullable();

      $table->string('poster_path')->nullable();

      $table->string('tmdb_id')->nullable();
      $table->boolean('is_published')->default(false);
      $table->boolean('is_hidden')->default(false);

      $table->unique(['show_id', 'season_number']);
      $table->unique(['show_id', 'slug']);

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('seasons');
  }
};
