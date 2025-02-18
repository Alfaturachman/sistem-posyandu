<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PortalController;

// Frontend Routes
Route::get('/', [HomeController::class, 'index']);
// Route::get('/portal', [PortalController::class, 'index']);

// Backend Routes
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::get('/dashboard', [DashboardController::class, 'index']);
