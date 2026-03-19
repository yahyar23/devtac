<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RechargeCard;
use App\Models\DriverDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RechargeController extends Controller
{
    /**
     * عملية شحن رصيد السائق باستخدام الكود
     */
    public function recharge(Request $request)
    {
        // 1. التحقق من إدخال الكود
        $request->validate([
            'code' => 'required|string|exists:recharge_cards,code',
        ], [
            'exists' => 'عذراً، هذا الكود غير صحيح أو غير موجود في النظام.'
        ]);

        return DB::transaction(function () use ($request) {
            // 2. البحث عن الكارت والتأكد أنه غير مستخدم (Lock for update للأمان)
            $card = RechargeCard::where('code', $request->code)
                                ->lockForUpdate()
                                ->first();

            if ($card->is_used) {
                return response()->json(['message' => 'عذراً، هذا الكود تم استخدامه مسبقاً.'], 422);
            }

            // 3. جلب بيانات السائق (المحفظة)
            $user = Auth::user();
            $driverDetail = $user->driverDetail;

            if (!$driverDetail) {
                return response()->json(['message' => 'هذه العملية متاحة للسائقين فقط.'], 403);
            }

            // 4. تنفيذ عملية الشحن (تحديث الرصيد + تحديث الكارت)
            $oldBalance = $driverDetail->wallet_balance;
            $driverDetail->increment('wallet_balance', $card->amount);

            $card->update([
                'is_used' => true,
                'used_by' => $user->id,
                'used_at' => now(),
            ]);

            // 5. إرجاع الرد النهائي لـ Flutter
            return response()->json([
                'message' => 'تم شحن الرصيد بنجاح',
                'amount_added' => $card->amount,
                'new_balance' => $driverDetail->wallet_balance,
                'transaction_id' => $card->id
            ], 200);
        });
    }
}