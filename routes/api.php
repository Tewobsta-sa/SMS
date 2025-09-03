<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReceiptController;

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
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Category Resource
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');

    // Fee Structure Resource
    Route::get('/fees', [FeeController::class, 'index'])->name('fees.index');
    Route::post('/fees', [FeeController::class, 'store'])->name('fees.store');
    // Consider adding: Route::get('/fees/{fee}', [FeeController::class, 'show'])->name('fees.show');

    // Invoice Resource
    Route::post('/invoices', [InvoiceController::class, 'generateAdminInvoice'])->name('invoices.store');
    Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');

    // Manual Payment Resource
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');

    // Receipt Resource
    Route::get('/receipts/{receipt}/download', [ReceiptController::class, 'download'])->name('receipts.download');
});


Route::middleware(['auth:sanctum', 'role:parent'])->prefix('parent')->name('parent.')->group(function () {
    
    // Online Payment Initiation
    Route::post('/payments/online', [PaymentController::class, 'payOnline'])->name('payments.online');

    // Payment Gateway Redirects (User is sent back here from Chapa)
    Route::get('/payments/success', [PaymentController::class, 'success'])->name('payments.success');
    Route::get('/payments/failed', function() { 
        return "Payment Failed"; // In a real app, return a view
    })->name('payments.failed');

});


Route::post('/chapa/callback', [PaymentController::class, 'chapaCallback'])->name('chapa.callback');


});
