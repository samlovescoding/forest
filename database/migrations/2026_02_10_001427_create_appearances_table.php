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
    Schema::create('appearances', function (Blueprint $table) {
      $table->id();
      $table->string('title');
      $table->string('slug');
      $table->string('type')->default('video'); // image or video
      $table->string('source');
      $table->unsignedBigInteger('person_id')->nullable();
      $table->unsignedBigInteger('film_id')->nullable();
      $table->unsignedBigInteger('show_id')->nullable();
      $table->unsignedBigInteger('season_id')->nullable();
      $table->unsignedBigInteger('episode_id')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('appearances');
  }
};
