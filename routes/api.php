<?php

use App\Http\Controllers\Api\AIHealthAdviceController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\SymptomController;
use App\Http\Controllers\TestSwaggerController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('symptoms', SymptomController::class);

    Route::get('/doctors/search', [DoctorController::class, 'search']);
    Route::get('/doctors', [DoctorController::class, 'index']);
    Route::get('/doctors/{id}', [DoctorController::class, 'show']);

    Route::apiResource('appointments', AppointmentController::class);

    Route::post('/ai/health-advice', [AIHealthAdviceController::class, 'generate']);
    Route::get('/ai/history', [AIHealthAdviceController::class, 'history']);
});

Route::get('/test-swagger', [TestSwaggerController::class, 'test']);

Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working',
    ]);
});
