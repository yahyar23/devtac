<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class DashboardStats extends Component
{
    public function render()
    {
        // 1. إجمالي المشاهدات والزيارات
        $totalViews = DB::table('visits')->count();

        // 2. عدد الزوار الفريدين (Unique IPs)
        $uniqueVisitors = DB::table('visits')->distinct('ip_address')->count('ip_address');

        // 3. عدد زوار اليوم
        $todayVisitors = DB::table('visits')->where('visit_date', now()->toDateString())->count();

        return view('livewire.admin.main-dashboard', [
            'totalViews' => $totalViews,
            'uniqueVisitors' => $uniqueVisitors,
            'todayVisitors' => $todayVisitors,
        ]);
    }
}