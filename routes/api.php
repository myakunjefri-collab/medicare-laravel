<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\QueueApiController;

// Public Authentication Endpoints
Route::post('/register', [AuthController::class, 'apiRegister']);
Route::post('/login', [AuthController::class, 'apiLogin']);

// Protected API Endpoints
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('queues', QueueApiController::class);
});

