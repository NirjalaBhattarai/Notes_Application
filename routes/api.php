<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\CategoryController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Make categories public so frontend can load them
Route::get('/categories', [CategoryController::class, 'index']);

// Test routes for debugging
Route::get('/test', function() {
    return response()->json(['message' => 'API is working!']);
});

// Use the auth:api middleware instead of jwt.auth
Route::middleware(['auth:api'])->group(function () {
    Route::get('/test-auth', function() {
        return response()->json(['message' => 'Auth working!', 'user' => auth()->user()->id]);
    });
    
    Route::get('/notes', [NoteController::class, 'index']);
    Route::post('/notes', [NoteController::class, 'store']);
    Route::get('/notes/{id}', [NoteController::class, 'show']);
    Route::put('/notes/{id}', [NoteController::class, 'update']);
    Route::delete('/notes/{id}', [NoteController::class, 'destroy']);
    
    Route::post('/categories', [CategoryController::class, 'store']);
});

