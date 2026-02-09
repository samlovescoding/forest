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
  Route::livewire('/people/{person}/edit', 'pages::people.edit')->name('people.edit');
  Route::livewire('/people/{person}', 'pages::people.view')->name('people.view');

  // Route::livewire('/members', "pages::members")->name("members");
  Route::livewire('/settings', 'pages::settings')->name('settings');
});
