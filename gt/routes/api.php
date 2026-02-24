<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [\App\Http\Controllers\Api\AuthController::class, 'me']);
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);

    Route::prefix('reports')->group(function () {
        Route::get('/questions', [\App\Http\Controllers\Api\ReportApiController::class, 'questions']);
        Route::get('/history', [\App\Http\Controllers\Api\ReportApiController::class, 'history']);
        Route::post('/submit', [\App\Http\Controllers\Api\ReportApiController::class, 'submit']);
    });
});
