<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\StudentApplicationController;

// Public routes for authentication and password reset
Route::post('/login', [LoginController::class, 'login']);
Route::post('/password/email', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('/password/reset', [PasswordResetController::class, 'reset'])->name('password.reset');

// Protected routes that require a valid Sanctum token
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Protected routes for specific roles
    Route::middleware('role:teacher')->group(function () {
        Route::get('/teacher-dashboard', function () {
            return response()->json(['message' => 'Welcome to the teacher dashboard!']);
        });
    });

    Route::middleware('role:student')->group(function () {
        Route::get('/student-dashboard', function () {
            return response()->json(['message' => 'Welcome to the student dashboard!']);
        });
    });

    Route::middleware('role:parent')->group(function () {
        Route::post('/student-applications', [StudentApplicationController::class, 'store']);
        Route::get('/student-applications', [StudentApplicationController::class, 'index']);
        Route::get('/student-applications/{id}', [StudentApplicationController::class, 'show']);
    });
});
