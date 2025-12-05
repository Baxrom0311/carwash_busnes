<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Barcha kerakli Controller'larni import qilamiz
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TenantController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\ServiceController;
use App\Http\Controllers\Api\V1\VehicleController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ShiftController; // Import qilishni unutmang
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\PaymeController;
// routes/api.php
use App\Http\Controllers\Api\V1\UserProfileAvatarController; // <<< IMPORT
use App\Http\Controllers\Api\V1\PaymentController;


// --- Autentifikatsiya uchun ochiq yo'l ---
Route::post('/v1/login', [AuthController::class, 'login']);
Route::post('/v1/otp/send', [AuthController::class, 'sendOtp']);
Route::post('/v1/otp/verify', [AuthController::class, 'verifyOtp']);
Route::post('/payme', [PaymeController::class, 'handle']);


// --- Faqat tizimga kirganlar uchun himoyalangan yo'llar ---
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('tenants', TenantController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('services', ServiceController::class);
    Route::apiResource('vehicles', VehicleController::class);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('payments', PaymentController::class); // <<< QO'SHILDI
    Route::post('/shifts/open', [ShiftController::class, 'store']);
    Route::put('/shifts/close', [ShiftController::class, 'update']);
    Route::get('/dashboard', DashboardController::class);
    Route::post('/users/{user}/avatar', UserProfileAvatarController::class);

});

