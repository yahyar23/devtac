<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // 1. التحقق من البيانات مع رسائل واضحة
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|unique:users,phone',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:driver,customer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'خطأ في البيانات المرسلة',
                'errors'  => $validator->errors()
            ], 422);
        }

        // 2. معالجة الصور
        $imgPersonal = $request->hasFile('img_personal') ? $request->file('img_personal')->store('drivers', 'public') : null;
        $imgIdFront  = $request->hasFile('img_id_front') ? $request->file('img_id_front')->store('drivers', 'public') : null;
        $imgIdBack   = $request->hasFile('img_id_back') ? $request->file('img_id_back')->store('drivers', 'public') : null;

        // ✅ الصور الجديدة للسيارة
        $imgCarFront = $request->hasFile('img_car_front') ? $request->file('img_car_front')->store('drivers', 'public') : null;
        $imgCarBack  = $request->hasFile('img_car_back') ? $request->file('img_car_back')->store('drivers', 'public') : null;

        // 3. إنشاء المستخدم
        try {
            $user = User::create([
                'name'          => $request->name,
                'phone'         => $request->phone,
                'password'      => Hash::make($request->password),
                'role'          => $request->role,
                'status'        => $request->role === 'driver' ? 'pending' : 'active',
                'balance'       => 0.00,

                'img_personal'  => $imgPersonal,
                'img_id_front'  => $imgIdFront,
                'img_id_back'   => $imgIdBack,

                // ✅ إضافة الحقول الجديدة
                'img_car_front' => $imgCarFront,
                'img_car_back'  => $imgCarBack,

                'car_color'     => $request->car_color,
                'car_plate'     => $request->car_plate,
                'car_brand'     => $request->car_brand,
                'car_model'     => $request->car_model,
                'car_year'     => $request->car_year,
            ]);

            $token = $user->createToken('taxiToken')->plainTextToken;

            return response()->json([
                'token' => $token,
                'user'  => $user
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'فشل الحفظ في قاعدة البيانات',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $user = User::where('phone', $request->phone)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'رقم الهاتف أو كلمة السر غير صحيحة'], 401);
        }

        $token = $user->createToken('taxiToken')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $user
        ], 200);
    }
}