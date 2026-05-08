<?php

use App\Http\Controllers\BrandController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TicketsController;
use App\Http\Controllers\CarController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user',    [AuthController::class, 'user']);
});

Route::controller(TicketsController::class)->group(function () {
    Route::post('/ticket', 'store');
    Route::get('/ticket', 'index');
});

Route::prefix('cars')->controller(CarController::class)->group(function () {
    Route::get('/', 'index');
    Route::post('/','store');
    Route::get('/{id}', 'show');
    Route::put('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
});

Route::prefix('brands')->controller(BrandController::class)->group(function () {
    Route::get('/', 'index');
});
