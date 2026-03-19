<?php

namespace App\Http\Controllers\Api; // التحديث للمجلد الجديد

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverDetailController extends Controller
{
    /**
     * 1. جلب ملف السائق الكامل
     * لعرض بيانات السيارة، الرصيد، وحالة التوثيق في تطبيق السائق
     */
    public function getProfile()
    {
        $user = Auth::user()->load('driverDetail');

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
                'car_info' => [
                    'model'  => $user->driverDetail->car_model,
                    'number' => $user->driverDetail->car_number,
                    'color'  => $user->driverDetail->car_color,
                ],
                'wallet_balance' => $user->driverDetail->wallet_balance,
                'is_verified'    => (bool) $user->driverDetail->is_verified, // حالة موافقة الإدارة
                'is_available'   => (bool) $user->driverDetail->is_available,
            ]
        ], 200);
    }

    /**
     * 2. تحديث الحالة والموقع (للخريطة الحية)
     * يتم استدعاؤه من Flutter لتحديث إحداثيات السائق وتواضده للخدمة
     */
    public function updateStatusAndLocation(Request $request)
    {
        $request->validate([
            'current_lat'  => 'required|numeric',
            'current_long' => 'required|numeric',
            'is_available' => 'required|boolean',
        ]);

        $driver = Auth::user()->driverDetail;

        if (!$driver) {
            return response()->json(['message' => 'سجل السائق غير موجود'], 404);
        }

        // تحديث الموقع والحالة في قاعدة البيانات
        $driver->update([
            'current_lat'  => $request->current_lat,
            'current_long' => $request->current_long,
            'is_available' => $request->is_available,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث الموقع والحالة بنجاح'
        ], 200);
    }

    /**
     * 3. جلب تاريخ الرحلات التي نفذها السائق
     */
    public function tripHistory()
    {
        $user = Auth::user();

        $trips = $user->tripsAsDriver()
            ->with(['customer' => function($query) {
                $query->select('id', 'name', 'phone', 'avatar');
            }])
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'trips' => $trips
        ], 200);
    }
}