<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DaftarAnakController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageCitraController;
use App\Http\Controllers\PeriksaAnakController;
use App\Http\Controllers\PortalController;
use Illuminate\Support\Facades\Route;

// =======================
// Frontend Routes (Portal)
// =======================
Route::controller(PortalController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/cari-pemeriksaan', 'cariPemeriksaan');
    Route::get('/portal/show/{nik}', 'show');
});

// =======================
// Authentication Routes
// =======================
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
});

// Forgot Password Route
Route::view('/forgot-password', 'backend.pages.auth.forgot-password')->name('password.request');

// =======================
// Backend Routes (Authenticated)
// =======================
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Daftar Anak
    Route::controller(DaftarAnakController::class)->prefix('daftar-anak')->group(function () {
        Route::get('/', 'index')->name('daftar-anak');
        Route::post('/store', 'store')->name('daftar.store');
    });

    // Periksa Anak
    Route::controller(PeriksaAnakController::class)->group(function () {
        Route::get('/hasil', 'hasil')->name('hasil');
        Route::get('/hasil/{id}', 'hasil_detail')->name('anak.detail');
        Route::get('/hasil/edit-identitas/{id}', 'edit_identitas')->name('edit-identitas');
        Route::post('/hasil/update-identitas/{id}', 'update_identitas')->name('update-identitas');
        Route::get('/periksa', 'periksa')->name('periksa');
        Route::post('/periksa/store', 'store')->name('periksa.store');
    });

    // Image Processing
    Route::post('/process-image', [ImageCitraController::class, 'processImage'])->name('process.image');
});
