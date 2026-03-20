<?php

namespace App\Http\Controllers\Api; // تأكد من إضافة Api إذا كان الملف داخل مجلد Api

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    public function recharge(Request $request)
    {
        // 1. التحقق من المدخلات
        $request->validate([
            'code' => 'required|string',
        ]);

        $code = $request->code;
        $user = $request->user(); 

        // 2. البحث عن الكود
        $voucher = DB::table('vouchers')->where('code', $code)->first();

        // 3. التحقق من الصلاحية
        if (!$voucher) {
            return response()->json([
                'status' => 'error',
                'message' => 'عذراً، كود الشحن هذا غير صحيح.'
            ], 404);
        }

        if ($voucher->is_used) {
            return response()->json([
                'status' => 'error',
                'message' => 'هذا الكود تم استخدامه مسبقاً.'
            ], 400);
        }

        // 4. تنفيذ العملية (Transaction)
        try {
            DB::transaction(function () use ($user, $voucher) {
                // تحديث رصيد الكابتن (تأكد أن الحقل اسمه balance في جدول users)
                DB::table('users')->where('id', $user->id)->increment('balance', $voucher->amount);

                // تحديث حالة الكود (استخدمنا used_by_driver_id ليتطابق مع المودل الخاص بك)
                DB::table('vouchers')->where('id', $voucher->id)->update([
                    'is_used' => true,
                    'used_by_driver_id' => $user->id, 
                    'used_at' => now(),
                    'updated_at' => now(),
                ]);
            });

            // 5. جلب الرصيد الجديد
            $newBalance = DB::table('users')->where('id', $user->id)->value('balance');

            return response()->json([
                'status' => 'success',
                'message' => 'تم شحن الرصيد بنجاح.',
                'amount' => $voucher->amount,
                'new_balance' => $newBalance
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء معالجة الطلب.'
            ], 500);
        }
    }
}