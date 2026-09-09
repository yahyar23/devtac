<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MainDashboard extends Component
{
    public $searchPlan = '';

    public function render()
    {
        // 1. إحصائيات الزوار من جدول visits
        $totalViews = Schema::hasTable('visits') ? DB::table('visits')->count() : 0;
        $uniqueVisitors = Schema::hasTable('visits') ? DB::table('visits')->distinct('ip_address')->count('ip_address') : 0;
        $todayVisitors = Schema::hasTable('visits') ? DB::table('visits')->whereDate('created_at', now()->toDateString())->count() : 0;

        // 2. إحصائيات الخطط مع فحص وجود عمود status بأمان
        $hasPlansTable = Schema::hasTable('patient_plans');
        $hasStatusColumn = $hasPlansTable && Schema::hasColumn('patient_plans', 'status');

        $totalPlans    = $hasPlansTable ? DB::table('patient_plans')->count() : 0;
        $optimalPlans  = $hasStatusColumn ? DB::table('patient_plans')->where('status', 'Optimal')->count() : 0;
        $underReview   = $hasStatusColumn ? DB::table('patient_plans')->where('status', 'Pending')->count() : 0;
        $rejectedPlans = $hasStatusColumn ? DB::table('patient_plans')->where('status', 'Rejected')->count() : 0;

        return view('livewire.admin.main-dashboard', [
            'stats' => [
                'total_views'     => $totalViews,
                'unique_visitors' => $uniqueVisitors,
                'today_visitors'  => $todayVisitors,
                'total_plans'     => $totalPlans,
                'optimal_plans'   => $optimalPlans,
                'under_review'    => $underReview,
                'rejected_plans'  => $rejectedPlans,
            ],
        ])->layout('layouts.app');
    }
}