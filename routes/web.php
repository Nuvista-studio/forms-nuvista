<?php

use App\Http\Controllers\ExportPdfController;
use App\Http\Controllers\UserCsvController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', fn () => redirect()->route('login'));

Route::post('logout', function () {
    Auth::guard('web')->logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect('/login');
})->middleware('auth')->name('logout');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', fn () => auth()->user()->hasRole('admin')
        ? redirect()->route('admin.dashboard')
        : redirect()->route('forms.search'))
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

    Volt::route('assets/{id}', 'pages.assets.show')
        ->name('assets.show');

    Volt::route('forms', 'pages.forms.search')
        ->name('forms.search');

    Route::get('pemeriksaan/{id}/export-pdf', [ExportPdfController::class, 'pemeriksaan'])
        ->name('pemeriksaan.export-pdf');

    Route::get('perawatan/{id}/export-pdf', [ExportPdfController::class, 'perawatan'])
        ->name('perawatan.export-pdf');

    // ── Admin Panel ──────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', fn () => redirect()->route('admin.dashboard'))
            ->name('index');

        Route::view('dashboard', 'admin-dashboard')
            ->name('dashboard');

        // Sites
        Volt::route('sites', 'admin.pages.sites.index')
            ->name('sites.index');

        Volt::route('sites/create', 'admin.pages.sites.create')
            ->name('sites.create');

        Volt::route('sites/{idSite}/edit', 'admin.pages.sites.edit')
            ->name('sites.edit');

        // Users
        Volt::route('users', 'admin.pages.users.index')
            ->name('users.index');

        Volt::route('users/create', 'admin.pages.users.create')
            ->name('users.create');

        Volt::route('users/{userId}/edit', 'admin.pages.users.edit')
            ->name('users.edit');

        Volt::route('users/import', 'admin.pages.users.import')
            ->name('users.import');

        Route::get('users/export-csv', [UserCsvController::class, 'export'])
            ->name('users.export.csv');

        Route::get('users/import/template', [UserCsvController::class, 'template'])
            ->name('users.import.template');
    });

    // ── Legacy user routes → redirect to admin ───────────────
    Route::middleware('role:admin')->group(function () {
        Route::get('users', fn () => redirect()->route('admin.users.index'))
            ->name('users.index');

        Route::get('users/create', fn () => redirect()->route('admin.users.create'))
            ->name('users.create');

        Route::get('users/{id}/edit', fn ($id) => redirect()->route('admin.users.edit', $id))
            ->name('users.edit');

        Route::get('users/import', fn () => redirect()->route('admin.users.import'))
            ->name('users.import');
    });
});

require __DIR__.'/auth.php';
