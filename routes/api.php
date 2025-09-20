<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;

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
    // Classes & Students
    Route::get('/my-classes', [TeacherController::class, 'getMyClasses']);
    Route::get('/assigned-classes', [TeacherController::class, 'getAssignedClasses']);
    Route::get('/students', [TeacherController::class, 'getStudentsByClass']);
    Route::get('/class-roster', [TeacherController::class, 'getClassRoster']);

    // Attendance
    Route::post('/attendance/mark', [TeacherController::class, 'markAttendance']);

    // Assignments
    Route::post('/assignments', [TeacherController::class, 'createAssignment']);

    // Grades
    Route::post('/grades', [TeacherController::class, 'enterGrades']);

    // Announcements
    Route::post('/announcements', [TeacherController::class, 'createAnnouncement']);

    // Profile
    Route::get('/profile', [TeacherController::class, 'getMyProfile']);
});

