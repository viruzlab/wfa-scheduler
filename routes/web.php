<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\WfaController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', [WfaController::class, 'index']);
Route::get('/api/schedule/{month}', [WfaController::class, 'getSchedule']);
Route::post('/api/book', [WfaController::class, 'store']);
Route::delete('/api/cancel', [WfaController::class, 'cancel']);

Route::middleware('auth')->group(function () {
    // Admin Routes
    Route::middleware('admin')->group(function () {
        Route::get('/admin', [WfaController::class, 'admin']);
        Route::post('/admin/dosen', [WfaController::class, 'storeDosen']);
        Route::post('/admin/setting', [WfaController::class, 'storeSetting']);
        Route::get('/api/admin/bookings', [WfaController::class, 'getAdminBookings']);
    });
});
