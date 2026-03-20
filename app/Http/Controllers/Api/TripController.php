<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TripController extends Controller
{
    /**
     * 1. طلب رحلة جديدة
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

        return response()->json([
            'message' => 'تم إرسال طلبك بنجاح',
            'id' => $trip->id,
            'trip' => $trip
        ], 201);
    }

    /**
     * 2. قبول الرحلة (السائق)
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

        return response()->json([
            'message' => 'تم قبول الرحلة بنجاح',
            'trip' => $trip->load('customer') 
        ]);
    }

    /**
     * 3. تحديث الحالة (وصلت / ركب الزبون)
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:arrived,ongoing' 
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $trip = Trip::where('id', $id)->where('driver_id', Auth::id())->firstOrFail();
        $trip->update(['status' => $request->status]);

        return response()->json([
            'message' => 'تم تحديث حالة الرحلة',
            'status' => $trip->status,
            'trip' => $trip->load(['customer', 'driver'])
        ]);
    }

    /**
     * 4. إنهاء الرحلة (تم التعديل لإزالة إجبارية الـ amount وحل خطأ 422)
     */
    public function completeTrip(Request $request, $id)
    {
        // نجلب الرحلة مع السائق للتأكد من البيانات
        $trip = Trip::where('id', $id)->where('driver_id', Auth::id())->firstOrFail();
        
        if ($trip->status === 'completed') {
            return response()->json(['message' => 'الرحلة مكتملة بالفعل'], 422);
        }

        return DB::transaction(function () use ($trip, $request) {
            $driver = Auth::user();
            
            // نأخذ المبلغ من الرحلة نفسها إذا لم يرسله التطبيق
            $finalFare = $request->amount ?? $trip->fare; 
            $commission = $finalFare * 0.12; // عمولة 12%

            // تحديث بيانات الرحلة
            $trip->update([
                'status'   => 'completed',
                'ended_at' => now(),
                'fare'     => $finalFare, 
                'is_paid'  => true
            ]);

            // خصم العمولة من رصيد السائق (لأن السائق استلم الكاش من الزبون)
            // الرصيد هنا يمثل ديون الشركة بذمة السائق أو محفظته الإلكترونية
            if ($driver) {
                $driver->decrement('balance', $commission);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'تم إنهاء الرحلة بنجاح، خصم عمولة الشركة (12%)',
                'fare' => $finalFare,
                'commission_deducted' => $commission,
                'new_balance' => $driver->fresh()->balance ?? 0
            ]);
        });
    }

    /**
     * 5. الرحلات المتاحة
     */
    public function availableTrips()
    {
        $trips = Trip::where('status', 'pending')
                     ->with('customer:id,name,phone') 
                     ->latest()
                     ->get();

        return response()->json($trips);
    }

    /**
     * 6. تفاصيل الرحلة
     */
    public function show($id)
    {
        $trip = Trip::with(['customer', 'driver'])->findOrFail($id);
        return response()->json($trip);
    }

    /**
     * 7. الرصيد
     */
    public function getBalance()
    {
        return response()->json(['balance' => Auth::user()->balance ?? 0]);
    }
}