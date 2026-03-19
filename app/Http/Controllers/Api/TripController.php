<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TripController extends Controller
{
    /**
     * 1. طلب رحلة جديدة (يستدعيه الراكب)
     */
    public function store(Request $request)
    {
        $request->validate([
            'pickup_location'  => 'required|string',
            'pickup_lat'       => 'required|numeric',
            'pickup_long'      => 'required|numeric',
            'dropoff_location' => 'required|string',
            'dropoff_lat'      => 'required|numeric',
            'dropoff_long'     => 'required|numeric',
            'fare'             => 'required|numeric',
        ]);

        $trip = Trip::create([
            'customer_id'      => Auth::id(),
            'pickup_location'  => $request->pickup_location,
            'pickup_lat'       => $request->pickup_lat,
            'pickup_long'      => $request->pickup_long,
            'dropoff_location' => $request->dropoff_location,
            'dropoff_lat'      => $request->dropoff_lat,
            'dropoff_long'     => $request->dropoff_long,
            'fare'             => $request->fare,
            'status'           => 'pending',
        ]);

        // ملاحظة: هنا يجب إرسال إشعار (Firebase) لجميع السائقين القريبين
        return response()->json(['message' => 'تم إرسال طلبك، بانتظار قبول سائق', 'trip' => $trip], 201);
    }

    /**
     * 2. قبول الرحلة (يستدعيه السائق)
     */
    public function acceptTrip(Request $request, $id)
    {
        $trip = Trip::findOrFail($id);

        if ($trip->status !== 'pending') {
            return response()->json(['message' => 'عذراً، هذه الرحلة لم تعد متاحة'], 422);
        }

        $trip->update([
            'driver_id' => Auth::id(),
            'status'    => 'accepted',
            'started_at' => now(),
        ]);

        return response()->json(['message' => 'تم قبول الرحلة بنجاح', 'trip' => $trip]);
    }

    /**
     * 3. إنهاء الرحلة ودفع الثمن (يستدعيه السائق عند الوصول)
     */
    public function completeTrip(Request $request, $id)
    {
        $trip = Trip::findOrFail($id);
        $driver = Auth::user()->driverDetail;

        return DB::transaction(function () use ($trip, $driver) {
            // تحديث حالة الرحلة
            $trip->update([
                'status'   => 'completed',
                'ended_at' => now(),
                'is_paid'  => true
            ]);

            // خصم عمولة الشركة (مثلاً 10%) من محفظة السائق إذا كان الدفع نقداً
            // أو إضافة الصافي للمحفظة إذا كان الدفع إلكترونياً
            $commission = $trip->fare * 0.10; 
            $driver->decrement('wallet_balance', $commission);

            return response()->json([
                'message' => 'تم إنهاء الرحلة وخصم العمولة',
                'fare' => $trip->fare,
                'new_balance' => $driver->wallet_balance
            ]);
        });
    }

    /**
     * 4. عرض الرحلات المتاحة (للسائقين فقط)
     */
    public function availableTrips()
    {
        $trips = Trip::where('status', 'pending')->with('customer')->latest()->get();
        return response()->json($trips);
    }
}