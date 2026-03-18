<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SymptomController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AiHealthAdviceController;

// AUTH ROUTES

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});


// PROTECTED ROUTES

Route::middleware('auth:sanctum')->group(function () {

    // Symptoms
    Route::apiResource('symptoms', SymptomController::class);

    // Doctors
    Route::get('/doctors', [DoctorController::class, 'index']);
    Route::get('/doctors/{id}', [DoctorController::class, 'show']);
    Route::get('/doctors-search', [DoctorController::class, 'search']);

    // Appointments
    Route::apiResource('appointments', AppointmentController::class);

    // AI Health Advice
    Route::post('/ai/health-advice', [AiHealthAdviceController::class, 'generateAdvice']);
    Route::get('/ai/history', [AiHealthAdviceController::class, 'history']);
});


// TEST ROUTE

Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working '
    ]);
});