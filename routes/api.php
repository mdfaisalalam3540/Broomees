<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RelationshipController;
use App\Http\Controllers\Api\HobbyController;
use App\Http\Controllers\Api\MetricsController;




// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/token', [AuthController::class, 'issueToken']);

// Protected routes with rate limiting and authentication
Route::middleware(['api.ratelimit', 'auth.api'])->group(function () {

    // User routes
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update'])->middleware('optimistic.lock');
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    // Relationship routes
    Route::post('/users/{id}/relationships', [RelationshipController::class, 'store']);
    Route::delete('/users/{id}/relationships', [RelationshipController::class, 'destroy']);

    // Hobby routes
    Route::post('/users/{id}/hobbies', [HobbyController::class, 'store']);
    Route::delete('/users/{id}/hobbies', [HobbyController::class, 'destroy']);

    // Metrics routes
    Route::get('/metrics/reputation', [MetricsController::class, 'reputation']);

    // Token revocation
    Route::post('/auth/revoke', [AuthController::class, 'revokeToken']);
});



