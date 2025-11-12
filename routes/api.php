<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\GoldPriceController;
use App\Http\Controllers\Api\MessageController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public product routes
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// Public gold price routes
Route::get('/gold-price/current', [GoldPriceController::class, 'getCurrentPrice']);

// Protected routes (require JWT authentication)
Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/upgrade-to-seller', [AuthController::class, 'upgradeToSeller']);

    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    Route::get('/messages', [MessageController::class, 'index']);
    Route::post('/messages', [MessageController::class, 'store']);
    Route::delete('/messages/{id}', [MessageController::class, 'destroy']);
    Route::post('/messages/delete-multiple', [MessageController::class, 'destroyMultiple']);
    Route::post('/messages/{id}/comments', [MessageController::class, 'addComment']);
    Route::post('/messages/{id}/interactions', [MessageController::class, 'toggleInteraction']);
});
