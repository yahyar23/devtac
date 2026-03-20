<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TripController;
use App\Http\Controllers\Api\VoucherController;
use App\Http\Controllers\Api\CustomerDetailController;
use App\Http\Controllers\Api\DriverDetailController;

/*
|--------------------------------------------------------------------------
| 1. Public Routes
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| 2. Protected Routes (auth:sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
Route::get('/profile', function (Request $request) {
        return $request->user();
    });

    // --- أ. نظام المصادقة والحساب ---
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user()->load(['driverDetail', 'customerDetail']);
    });
     Route::post('/recharge', [VoucherController::class, 'recharge']);

    // --- ج. لوحة تحكم السائق (Driver Panel) ---
    Route::prefix('driver')->group(function () {
        Route::get('/profile', [DriverDetailController::class, 'getProfile']);
        Route::post('/update-status', [DriverDetailController::class, 'updateStatusAndLocation']);
        Route::get('/history', [DriverDetailController::class, 'tripHistory']);
        Route::get('/nearby', [TripController::class, 'nearbyDrivers']);
        Route::post('/update-location', [TripController::class, 'updateLocation']);
        // --- إضافة مسار الرصيد للسائق (مطلوب لشاشة الكابتن في Flutter) ---
        Route::get('/balance', [TripController::class, 'getBalance']); 

    });

    // --- د. نظام الرحلات الأساسي (Trip System) ---
    Route::prefix('trips')->group(function () {
        Route::get('/available', [TripController::class, 'availableTrips']); 
        Route::delete('/{id}/timeout-cancel', [TripController::class, 'timeoutCancel']);
        Route::post('/create', [TripController::class, 'store']);            
        Route::get('/{id}', [TripController::class, 'show']); // --- مهم جداً للزبون لمتابعة حالة الكابتن ---
        
        Route::post('/{id}/accept', [TripController::class, 'acceptTrip']);   
        
        // --- مسار تحديث الحالة (وصلت / ركب الزبون) ---
        Route::post('/{id}/status', [TripController::class, 'updateStatus']); 
        
        // --- مسار إنهاء الرحلة (تأكد من تسميته finish ليطابق كود Flutter أو عدل Flutter إلى complete) ---
        Route::post('/{id}/finish', [TripController::class, 'completeTrip']); 
    });

});