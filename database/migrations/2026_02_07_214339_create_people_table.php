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
    Schema::create('people', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('slug');
      $table->string('full_name');
      $table->date('birth_date');
      $table->date('death_date')->nullable();
      $table->string('gender');
      $table->string('sexuality');
      $table->string('birth_country');
      $table->string('birth_city');
      $table->string('picture')->nullable();

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
    Schema::dropIfExists('people');
  }
};
