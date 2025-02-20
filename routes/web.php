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

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/forgot-password', function () {
    return view('backend.pages.auth.forgot-password');
})->name('password.request');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/daftar-anak', [DaftarAnakController::class, 'index'])->name('daftar-anak');
Route::get('/anak/create', [DaftarAnakController::class, 'create'])->name('anak.create');
Route::post('/anak/store', [DaftarAnakController::class, 'store'])->name('anak.store');

Route::get('/hasil', [PeriksaAnakController::class, 'hasil'])->name('hasil');
Route::get('/hasil/{id}', [PeriksaAnakController::class, 'hasil_detail'])->name('anak.detail');
Route::get('/periksa', [PeriksaAnakController::class, 'periksa'])->name('periksa');