<?php

namespace App\Http\Controllers\Api; // تم التحديث ليتناسب مع المجلد الجديد

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerDetailController extends Controller
{
    /**
     * 1. جلب بيانات ملف الزبون الكاملة
     * تستخدم لعرض الرصيد والتقييم في الشاشة الرئيسية لتطبيق الفلاتر
     */
    public function getProfile()
    {
        // جلب المستخدم مع تفاصيل الزبون المرتبطة به
        $user = Auth::user()->load('customerDetail');

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
                'wallet_balance' => $user->customerDetail->wallet_balance ?? 0.00,
                'rating' => $user->customerDetail->rating ?? 5.0,
                'total_trips' => $user->customerDetail->total_trips ?? 0,
            ]
        ], 200);
    }

    /**
     * 2. تحديث الموقع الجغرافي الأخير للزبون
     * يتم استدعاؤه دورياً من تطبيق الموبايل لتحديث مكان الزبون على الخريطة
     */
    public function updateLocation(Request $request)
    {
        $request->validate([
            'last_lat'  => 'required|numeric',
            'last_long' => 'required|numeric',
        ]);

        $user = Auth::user();
        
        // التأكد من وجود سجل تفاصيل للزبون
        $customer = $user->customerDetail;
        
        if (!$customer) {
            return response()->json(['message' => 'سجل تفاصيل الزبون غير موجود'], 404);
        }

        $customer->update([
            'last_lat'  => $request->last_lat,
            'last_long' => $request->last_long,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث الموقع بنجاح'
        ], 200);
    }

    /**
     * 3. جلب تاريخ الرحلات (History)
     * يعرض للزبون قائمة بالرحلات التي قام بها سابقاً مع بيانات السائقين
     */
    public function tripHistory()
    {
        $user = Auth::user();

        // جلب الرحلات مع بيانات السائق لكل رحلة وترتيبها من الأحدث للأقدم
        $trips = $user->tripsAsCustomer()
            ->with(['driver' => function($query) {
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