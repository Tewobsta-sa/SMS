<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\PaymentController;

Route::get('/test', function () {
    return response()->json(['status' => 'api.php is working']);
});


// Public routes for authentication and password reset
Route::post('/login', [LoginController::class, 'login'])->name('login');
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
});

use App\Http\Controllers\Api\TeacherController;

Route::middleware(['auth:sanctum'])->prefix('teacher')->group(function () {

    Route::get('/me/classes', [TeacherController::class, 'getMyClasses']);

    Route::get('/classes/{classId}/roster', [TeacherController::class, 'getClassRoster']);

    Route::post('/classes/{classId}/attendance', [TeacherController::class, 'markAttendance']);

    // 4. Assignments
    Route::get('/classes/{classId}/assignments', [TeacherController::class, 'getAssignmentsByClass']);
    Route::post('/classes/{classId}/assignments', [TeacherController::class, 'createAssignment']);

    // 5. Grades
    Route::post('/classes/{classId}/grades', [TeacherController::class, 'enterGrades']);

    // 6. Announcements
    Route::post('/classes/{classId}/announcements', [TeacherController::class, 'createAnnouncement']);

});

    Route::post('/payments/init', [PaymentController::class, 'initialize'])->name('payment.init');
    Route::get('/payments/status', [PaymentController::class, 'status'])->name('payment.status');
    Route::get('/payments/return', [PaymentController::class, 'return'])->name('payment.return');
    Route::post('/payments/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');


