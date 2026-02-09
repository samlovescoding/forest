<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  use WithoutModelEvents;

  public function run(): void
  {
    if (app()->environment('local')) {
      $this->local();
    }
  }

  public function local(): void
  {
    $this->call([
        PersonSeeder::class,
        FilmSeeder::class,
        ShowSeeder::class,
        AdministratorSeeder::class,
    ]);
  }
}
