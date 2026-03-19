<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// استدعاء كافة الكنترولرات من مجلد Api
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TripController;
use App\Http\Controllers\Api\RechargeController;
use App\Http\Controllers\Api\CustomerDetailController;
use App\Http\Controllers\Api\DriverDetailController;

/*
|--------------------------------------------------------------------------
| 1. Public Routes (المسارات العامة - المتاحة للجميع)
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


/*
|--------------------------------------------------------------------------
| 2. Protected Routes (المسارات المحمية - تتطلب Token)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // --- أ. نظام المصادقة والحساب ---
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user()->load(['driverDetail', 'customerDetail']);
    });

    // --- ب. لوحة تحكم الزبون (Customer Panel) ---
    Route::prefix('customer')->group(function () {
        Route::get('/profile', [CustomerDetailController::class, 'getProfile']);
        Route::post('/update-location', [CustomerDetailController::class, 'updateLocation']);
        Route::get('/history', [CustomerDetailController::class, 'tripHistory']);
    });

    // --- ج. لوحة تحكم السائق (Driver Panel) ---
    Route::prefix('driver')->group(function () {
        Route::get('/profile', [DriverDetailController::class, 'getProfile']);
        Route::post('/update-status', [DriverDetailController::class, 'updateStatusAndLocation']);
        Route::get('/history', [DriverDetailController::class, 'tripHistory']);
        // نظام الشحن خاص بالسائقين
        Route::post('/recharge', [RechargeController::class, 'recharge']);
    });

    // --- د. نظام الرحلات الأساسي (Trip System) ---
    Route::prefix('trips')->group(function () {
        Route::get('/available', [TripController::class, 'availableTrips']); // للسائقين لرؤية الطلبات
        Route::post('/create', [TripController::class, 'store']);            // للزبون لطلب رحلة
        Route::post('/{id}/accept', [TripController::class, 'acceptTrip']);   // للسائق لقبول الرحلة
        Route::post('/{id}/complete', [TripController::class, 'completeTrip']); // للسائق لإنهاء الرحلة
    });

});