<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Volt::route('dashboard', 'dashboard')
        ->name('dashboard');

    Volt::route('profile', 'profile')
        ->name('profile');

    Volt::route('pemeriksaan/create', 'pages.pemeriksaan.create')
        ->name('pemeriksaan.create');

    Volt::route('perawatan/create', 'pages.perawatan.create')
        ->name('perawatan.create');

    Volt::route('pemeriksaan/{id}/signature', 'pages.pemeriksaan.signature')
        ->name('pemeriksaan.signature');

    Volt::route('perawatan/{id}/signature', 'pages.perawatan.signature')
        ->name('perawatan.signature');
});

require __DIR__.'/auth.php';
