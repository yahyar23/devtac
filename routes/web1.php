<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\PlanEvaluator; // مسار مكون Livewire للفحص

/*
|--------------------------------------------------------------------------
| المسارات العامة
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

Route::get('/plan-print/{id}', function ($id) {
    $plan = \App\Models\PatientPlan::findOrFail($id);
    return view('reports.plan-print', compact('plan'));
})->name('plan.print');
Route::get('/', PlanEvaluator::class)->name('plan.evaluator');

/*
|--------------------------------------------------------------------------
| المسارات المحمية (بعد تسجيل الدخول)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

});

// ملف مسارات Breeze (Login, Register, etc.)
require __DIR__.'/auth.php';