<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Admin\MainDashboard;
use App\Http\Livewire\PlanEvaluator; // مسار مكون Livewire للفحص
use App\Http\Livewire\Admin\SiteSettings;
/*
|--------------------------------------------------------------------------
| المسارات العامة
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

/*


|--------------------------------------------------------------------------
| المسارات المحمية (بعد تسجيل الدخول)
|--------------------------------------------------------------------------
*/

Route::get('/plan-print/{id}', function ($id) {
    $plan = \App\Models\PatientPlan::findOrFail($id);
    return view('reports.plan-print', compact('plan'));
})->name('plan.print');
Route::get('/', PlanEvaluator::class)->name('plan.evaluator');
Route::middleware(['auth', 'verified'])->group(function () {
Route::get('/admin/settings', SiteSettings::class)->name('admin.settings');
    // التوجيه التلقائي المباشر للوحة الأدمن
    Route::get('/dashboard', function () {
        return redirect()->route('admin.dashboard');
    })->name('dashboard');

    // مسار لوحة تحكم الأدمن لتقييم العلاج الإشعاعي
    Route::get('/admin/dashboard', MainDashboard::class)
        ->name('admin.dashboard');
});

// مسارات المصادقة (Laravel Breeze / Fortify)
require __DIR__.'/auth.php';