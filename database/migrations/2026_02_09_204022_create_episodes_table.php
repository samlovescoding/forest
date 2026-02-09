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
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('season_id')->constrained()->cascadeOnDelete();
            $table->foreignId('show_id')->constrained()->cascadeOnDelete();

            $table->string('name');
            $table->string('slug');
            $table->text('overview')->nullable();
            $table->integer('episode_number');
            $table->integer('season_number');
            $table->integer('runtime')->nullable();
            $table->date('air_date')->nullable();
            $table->string('production_code')->nullable();

            $table->integer('vote_count')->nullable();
            $table->double('vote_average')->nullable();

            $table->string('still_path')->nullable();

            $table->string('tmdb_id')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_hidden')->default(false);

            $table->unique(['season_id', 'episode_number']);
            $table->unique(['season_id', 'slug']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};
