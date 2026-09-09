<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrackVisits
{
    public function handle(Request $request, Closure $next)
    {
        // يسجل الزيارة للطلبات العادية عند زيارة صفحات الموقع (GET) وليس لطلبات Livewire/AJAX
        if (!$request->header('X-Livewire') && !$request->ajax() && $request->isMethod('get')) {
            
            $userIp = $request->ip();
            $today = now()->toDateString();

            // التحقق من عدم وجود تسجيل سابق لنفس الـ IP في نفس اليوم
            $alreadyVisitedToday = DB::table('visits')
                ->where('ip_address', $userIp)
                ->whereDate('visit_date', $today)
                ->exists();

            // الإضافة فقط إذا لم يسجل دخوله اليوم
            if (!$alreadyVisitedToday) {
                DB::table('visits')->insert([
                    'ip_address' => $userIp,
                    'url'        => $request->fullUrl(),
                    'visit_date' => $today,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return $next($request);
    }
}