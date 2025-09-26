<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

Route::put('/weight', [ApiController::class, 'ApiWeight']);
Route::put('/height', [ApiController::class, 'ApiHeight']);
Route::put('/had', [ApiController::class, 'ApiHad']);
Route::put('/arm', [ApiController::class, 'ApiArm']);