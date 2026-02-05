<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', "pages::welcome")->name("home");
Route::livewire('/login', "pages::login")->name("login");
Route::livewire('/register', "pages::register")->name("register");
Route::livewire('/recovery', "pages::recovery")->name("recovery");
Route::livewire('/verify', "pages::verification")->name("verification");


Route::middleware("auth")->group(function () {
  Route::livewire('/members', "pages::members")->name("members");
  Route::livewire('/settings', "pages::settings")->name("settings");
});
