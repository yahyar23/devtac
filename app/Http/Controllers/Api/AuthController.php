<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. التحقق من المدخلات
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'يرجى إدخال الهاتف وكلمة السر'], 422);
        }

        // 2. البحث عن المستخدم
        $user = User::where('phone', $request->phone)->first();

        // 3. التحقق من كلمة السر
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'بيانات الدخول غير صحيحة'], 401);
        }

        // 4. توليد التوكن (تأكد من تنصيب Laravel Sanctum وهو افتراضي في النسخ الحديثة)
        $token = $user->createToken('taxiToken')->plainTextToken;

        // 5. الرد الذي ينتظره تطبيق الفلاتر (مهم جداً تطابق الأسماء)
        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'role' => $user->role,     // تأكد أن هذا الحقل موجود في جدول users
                'status' => $user->status, // pending أو active
            ]
        ], 200);
    }
}