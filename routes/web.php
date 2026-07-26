<?php

use App\Http\Controllers\ExportPdfController;
use App\Http\Controllers\UserCsvController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Volt::route('dashboard', 'dashboard')
        ->name('dashboard');

    Route::view('profile', 'profile')
        ->name('profile');

    Volt::route('pemeriksaan/create', 'pages.pemeriksaan.create')
        ->name('pemeriksaan.create');

    Volt::route('perawatan/create', 'pages.perawatan.create')
        ->name('perawatan.create');

    Volt::route('pemeriksaan/{id}/signature', 'pages.pemeriksaan.signature')
        ->name('pemeriksaan.signature');

    Volt::route('perawatan/{id}/signature', 'pages.perawatan.signature')
        ->name('perawatan.signature');

    Volt::route('approval/{type}/{id}', 'pages.approval.show')
        ->name('approval.show');

    Volt::route('assets', 'pages.assets.index')
        ->name('assets.index');

    Volt::route('users', 'pages.users.index')
        ->name('users.index')
        ->middleware('role:admin');

    Volt::route('users/create', 'pages.users.create')
        ->name('users.create')
        ->middleware('role:admin');

    Volt::route('users/{id}/edit', 'pages.users.edit')
        ->name('users.edit')
        ->middleware('role:admin');

    Volt::route('users/import', 'pages.users.import')
        ->name('users.import')
        ->middleware('role:admin');

    Route::get('users/export-csv', [UserCsvController::class, 'export'])
        ->name('users.export.csv')
        ->middleware('role:admin');

    Route::get('users/import/template', [UserCsvController::class, 'template'])
        ->name('users.import.template')
        ->middleware('role:admin');

    Volt::route('assets/{id}', 'pages.assets.show')
        ->name('assets.show');

    Volt::route('forms', 'pages.forms.search')
        ->name('forms.search');

    Route::get('pemeriksaan/{id}/export-pdf', [ExportPdfController::class, 'pemeriksaan'])
        ->name('pemeriksaan.export-pdf');

    Route::get('perawatan/{id}/export-pdf', [ExportPdfController::class, 'perawatan'])
        ->name('perawatan.export-pdf');
});

require __DIR__.'/auth.php';
