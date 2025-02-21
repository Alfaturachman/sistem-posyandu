<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DaftarAnakController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PeriksaAnakController;
use App\Http\Controllers\PortalController;

// Frontend Routes
Route::get('/', [PortalController::class, 'index']);
Route::get('/cari-pemeriksaan', [PortalController::class, 'cariPemeriksaan']);
Route::get('/portal/show/{nik}', [PortalController::class, 'show']);
// Route::get('/portal', [PortalController::class, 'index']);

// Backend Routes Login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/forgot-password', function () {
    return view('backend.pages.auth.forgot-password');
})->name('password.request');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Backend Routes Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Backend Routes Daftar Anak
Route::get('/daftar-anak', [DaftarAnakController::class, 'index'])->name('daftar-anak');
Route::post('/daftar/store', [DaftarAnakController::class, 'store'])->name('daftar.store');

// Backend Routes Periksa Anak
Route::get('/hasil', [PeriksaAnakController::class, 'hasil'])->name('hasil');
Route::get('/hasil/{id}', [PeriksaAnakController::class, 'hasil_detail'])->name('anak.detail');
Route::get('/hasil/edit-identitas/{id}', [PeriksaAnakController::class, 'edit_identitas'])->name('edit-identitas');
Route::post('/hasil/update-identitas/{id}', [PeriksaAnakController::class, 'update_identitas'])->name('update-identitas');
Route::get('/periksa', [PeriksaAnakController::class, 'periksa'])->name('periksa');
Route::post('/periksa/store', [PeriksaAnakController::class, 'store'])->name('periksa.store');