<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdministratorSeeder extends Seeder
{
  public function run(): void
  {
    User::query()->updateOrCreate(
      ['email' => 'admin@forest.test'],
      [
        'name' => 'Administrator',
        'password' => 'helloworld',
        'email_verified_at' => now(),
      ],
    );
  }
}
