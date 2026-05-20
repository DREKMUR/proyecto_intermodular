<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\OpinionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TicketsController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\OrderController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user',    [AuthController::class, 'user']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/my-orders', [OrderController::class, 'myOrders']);
    Route::put('/profile', [ProfileController::class, 'update']);

    Route::get('/check-pending-opinion', [OpinionController::class, 'checkPendingOpinion']);
    Route::post('/dismiss-pending-opinion', [OpinionController::class, 'dismissPendingOpinion']);
});

Route::post('/ticket', [TicketsController::class, 'store']);
Route::get('/ticket', [TicketsController::class, 'index'])->middleware('auth:sanctum');

Route::prefix('cars')->controller(CarController::class)->group(function () {
    Route::get('/', 'index');
    Route::post('/','store');
    Route::get('/{id}', 'show');
    Route::put('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
});

Route::controller(OpinionController::class)->group(function () {
    Route::get('/getOpinions/{idProducte}',  'getOpinions');
    Route::post('/sendOpinion',              'sendOpinion');
    Route::get('/getRating',                 'getRating');
    Route::get('/getAllOpinions',            'getAllOpinions');
});

Route::prefix('brands')->controller(BrandController::class)->group(function () {
    Route::get('/', 'index');
});

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::controller(AdminController::class)->group(function () {
        Route::get('/dashboard',           'dashboard');
        Route::get('/products',            'products');
        Route::post('/products',           'storeProduct');
        Route::put('/products/{id}',       'updateProduct');
        Route::delete('/products/{id}',    'deleteProduct');
        Route::post('/products/bulk-discount', 'bulkDiscount');
        Route::get('/brands',              'brands');
        Route::post('/brands',             'storeBrand');
        Route::put('/brands/{id}',         'updateBrand');
        Route::delete('/brands/{id}',      'deleteBrand');
        Route::get('/orders',              'orders');
        Route::put('/orders/{id}',         'updateOrder');
        Route::delete('/orders/{id}',      'deleteOrder');
        Route::get('/users',               'users');
        Route::put('/users/{id}',          'updateUser');
        Route::delete('/users/{id}',       'deleteUser');
        Route::get('/sales',               'salesData');
    });

    Route::controller(TicketsController::class)->group(function () {
        Route::get('/tickets',             'adminIndex');
        Route::put('/tickets/{id}/status', 'updateStatus');
    });
});
