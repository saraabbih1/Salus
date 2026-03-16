<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/me', function () {
    return response()->json([
        'success' => true,
        'user' => auth()->user(),
        'message' => 'API working'
    ]);
});