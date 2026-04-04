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
        $validator = Validator::make($request->all(), [
            'pickup_location'  => 'required|string',
            'pickup_lat'       => 'required|numeric',
            'pickup_long'      => 'required|numeric',
            'dropoff_location' => 'required|string',
            'dropoff_lat'      => 'required|numeric',
            'dropoff_long'     => 'required|numeric',
            'fare'             => 'required|numeric',
            'receiver_name'             => 'required|string',
            'receiver_phone'             => 'required|string',
            'item_type'             => 'required|string',
            'item_price'             => 'required|string',
            'items_count'             => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $trip = Trip::create([
            'customer_id'      => Auth::id(),
            'pickup_location'  => $request->pickup_location,
            'pickup_lat'       => $request->pickup_lat,
            'pickup_long'      => $request->pickup_long,
            'dropoff_location' => $request->dropoff_location,
            'dropoff_lat'      => $request->dropoff_lat,
            'dropoff_long'     => $request->dropoff_long,
            'fare'             => $request->fare,
            'receiver_name'             => $request->receiver_name,
            'receiver_phone'             => $request->receiver_phone,
            'item_type'             => $request->item_type,
            'item_price'             => $request->item_price,
            'items_count'             => $request->items_count,
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
     * تم دمج منطق الـ 60 ثانية لضمان جودة الطلبات
     */
    public function acceptTrip(Request $request, $id)
    {
        $trip = Trip::find($id);

        if (!$trip) {
            return response()->json(['message' => 'عذراً، الرحلة غير موجودة'], 404);
        }

        // منع قبول الرحلات التي مر عليها أكثر من دقيقة لضمان عدم انتظار الزبون طويلاً
        if ($trip->status === 'pending' && $trip->created_at->diffInSeconds(now()) > 60) {
            $trip->delete(); 
            return response()->json(['message' => 'عذراً، هذه الرحلة انتهت صلاحيتها'], 410);
        }

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
            'trip' => $trip->load(['customer', 'driver']) 
        ]);
    }

    /**
     * إلغاء الرحلة من قبل الزبون عند تأخر السائق (Timeout)
     */
    public function timeoutCancel($id)
    {
        $trip = Trip::where('id', $id)
                    ->where('customer_id', Auth::id())
                    ->where('status', 'pending')
                    ->first();

        if ($trip) {
            $trip->delete();
            return response()->json(['message' => 'تم إلغاء طلبك لعدم توفر كابتن قريب']);
        }

        return response()->json(['message' => 'لا يمكن الإلغاء حالياً'], 400);
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
     * 4. إنهاء الرحلة وخصم العمولة (12%)
     */
    public function completeTrip(Request $request, $id)
    {
        $trip = Trip::where('id', $id)->where('driver_id', Auth::id())->firstOrFail();
        
        if ($trip->status === 'completed') {
            return response()->json(['message' => 'الرحلة مكتملة بالفعل'], 422);
        }

        return DB::transaction(function () use ($trip, $request) {
            $user = Auth::user();
            
            // استخدام المبلغ المرسل أو المسجل مسبقاً
            $finalFare = $request->amount ?? $trip->fare; 
            $commission = $finalFare * 0.12; 

            // تحديث بيانات الرحلة النهائية
            $trip->update([
                'status'   => 'completed',
                'ended_at' => now(),
                'fare'     => $finalFare, 
                'is_paid'  => true
            ]);

            // خصم العمولة من رصيد السائق (تحويلها لدين)
            // ملاحظة: الحقل balance يجب أن يكون من نوع decimal في قاعدة البيانات
            if ($user) {
                $user->decrement('balance', $commission);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'تم إنهاء الرحلة بنجاح، تم قيد عمولة الشركة بذمتكم',
                'fare' => $finalFare,
                'commission_deducted' => $commission,
                'new_balance' => $user->fresh()->balance ?? 0
            ]);
        });
    }

    /**
     * 5. الرحلات المتاحة (للرادار)
     * تظهر فقط الرحلات الحديثة (أقل من 60 ثانية)
     */
    public function availableTrips()
    {
        $trips = Trip::where('status', 'pending')
                     ->where('created_at', '>=', now()->subSeconds(60))
                     ->with('customer:id,name,phone') 
                     ->latest()
                     ->get();

        return response()->json($trips);
    }

    /**
     * 6. تفاصيل الرحلة للتحديث اللحظي في فلاتر
     */
    public function show($id)
    {
        $trip = Trip::with([
            'customer:id,name,phone', 
            'driver:id,name,phone,lat,lng,heading' 
        ])->find($id);

        if (!$trip) {
            return response()->json(['message' => 'الرحلة غير موجودة'], 404);
        }

        return response()->json($trip);
    }

    /**
     * 7. تحديث موقع السائق اللحظي (يستدعى كل 5 ثواني من فلاتر)
     */
    public function updateLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lat'     => 'required|numeric',
            'lng'     => 'required|numeric',
            'heading' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        if ($user) {
            $user->update([
                'lat'                  => $request->lat,
                'lng'                  => $request->lng,
                'heading'              => $request->heading ?? 0,
                'last_location_update' => now(),
            ]);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * 8. جلب الرصيد الحالي
     */
    public function getBalance()
    {
        return response()->json(['balance' => Auth::user()->balance ?? 0]);
    }
}