<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::index')->name('index');

Route::middleware('layout:auth')->group(function () {
  Route::livewire('/login', 'pages::login')->name('login');
  Route::livewire('/register', 'pages::register')->name('register');
  Route::livewire('/recovery', 'pages::recovery')->name('recovery');
  Route::livewire('/verify', 'pages::verification')->name('verification');
});

Route::middleware(['verified', 'layout:dashboard'])->group(function () {
  Route::livewire('/home', 'pages::home')->name('home');

  // People
  Route::livewire('/people', 'pages::people.index')->name('people.index');
  Route::livewire('/people/create', 'pages::people.create')->name('people.create');
  Route::livewire('/people/import', 'pages::people.import')->name('people.import');
  Route::livewire('/people/{person}/edit', 'pages::people.edit')->name('people.edit');
  Route::livewire('/people/{person}', 'pages::people.view')->name('people.view');

  // Films
  Route::livewire('/films', 'pages::films.index')->name('films.index');
  Route::livewire('/films/create', 'pages::films.create')->name('films.create');
  Route::livewire('/films/import', 'pages::films.import')->name('films.import');
  Route::livewire('/films/{film}/edit', 'pages::films.edit')->name('films.edit');
  Route::livewire('/films/{film}', 'pages::films.view')->name('films.view');

  // Shows
  Route::livewire('/shows', 'pages::shows.index')->name('shows.index');
  Route::livewire('/shows/create', 'pages::shows.create')->name('shows.create');
  Route::livewire('/shows/import', 'pages::shows.import')->name('shows.import');
  Route::livewire('/shows/{show}/edit', 'pages::shows.edit')->name('shows.edit');
  Route::livewire('/shows/{show}', 'pages::shows.view')->name('shows.view');

  // Seasons
  Route::livewire('/shows/{show}/seasons', 'pages::shows.seasons.index')->name('seasons.index');
  Route::livewire('/shows/{show}/seasons/create', 'pages::shows.seasons.create')->name('seasons.create');
  Route::livewire('/shows/{show}/seasons/{season}/edit', 'pages::shows.seasons.edit')->name('seasons.edit');
  Route::livewire('/shows/{show}/seasons/{season}', 'pages::shows.seasons.view')->name('seasons.view');

  // Episodes
  Route::livewire('/shows/{show}/seasons/{season}/episodes', 'pages::shows.seasons.episodes.index')->name('episodes.index');
  Route::livewire('/shows/{show}/seasons/{season}/episodes/create', 'pages::shows.seasons.episodes.create')->name('episodes.create');
  Route::livewire('/shows/{show}/seasons/{season}/episodes/{episode}/edit', 'pages::shows.seasons.episodes.edit')->name('episodes.edit');
  Route::livewire('/shows/{show}/seasons/{season}/episodes/{episode}', 'pages::shows.seasons.episodes.view')->name('episodes.view');

  // People
  Route::livewire('/appearances', 'pages::appearances.index')->name('appearances.index');
  Route::livewire('/appearances/create', 'pages::appearances.create')->name('appearances.create');
  Route::livewire('/appearances/{appearance}/edit', 'pages::appearances.edit')->name('appearances.edit');
  Route::livewire('/appearances/{appearance}', 'pages::appearances.view')->name('appearances.view');

  // Route::livewire('/members', "pages::members")->name("members");
  Route::livewire('/settings', 'pages::settings')->name('settings');
});
