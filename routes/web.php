<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Driver\Dashboard as DriverDashboard;
use App\Http\Livewire\Admin\MainDashboard as AdminDashboard; // تم استيراد المكون الحقيقي

/*
|--------------------------------------------------------------------------
| المسارات العامة
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| المسارات المحمية (بعد تسجيل الدخول)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // 1. الموجه الذكي (The Router)
    // هذا هو العقل المدبر الذي يوزع المستخدمين حسب رتبهم
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;

        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($role === 'driver') {
            return redirect()->route('driver.dashboard');
        }
        
        return view('dashboard'); // للزبون العادي
    })->name('dashboard');

    // 2. لوحة تحكم السائق (صاحب البطاقة البنفسجية)
    Route::get('/driver/dashboard', DriverDashboard::class)
        ->name('driver.dashboard');

    // 3. لوحة تحكم الأدمن (غرفة العمليات)
    // قمنا بتغيير الـ Closure إلى المكون الحقيقي AdminDashboard
    Route::get('/admin/dashboard', AdminDashboard::class)
        ->name('admin.dashboard');

});

// ملف مسارات Breeze (Login, Register, etc.)
require __DIR__.'/auth.php';