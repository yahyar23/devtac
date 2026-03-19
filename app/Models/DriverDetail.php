<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverDetail extends Model
{
    use HasFactory;

    /**
     * الحقول المسموح بتعبئتها من خلال الطلبات الخارجية (API)
     * ملاحظة أمنية: تم استبعاد الحقول الحساسة مثل is_verified أو wallet_balance
     */
    protected $fillable = [
        'user_id', 
        'car_model', 
        'car_number', 
        'car_color', 
        'license_number',
        'id_card_image',    // مسار صورة الهوية
        'personal_image',   // مسار الصورة الشخصية
    ];

    /**
     * تحديد أنواع البيانات عند استخراجها من قاعدة البيانات
     * لضمان أن Flutter يستلم الأرقام كـ Double وليس كـ String
     */
    protected $casts = [
        'is_available' => 'boolean',
        'current_lat'  => 'double',
        'current_long' => 'double',
    ];

    /**
     * الحقول التي نريد إخفاءها عند إرسال استجابة JSON لتطبيق الموبايل
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    // ========================================================
    // العلاقات (Relationships)
    // ========================================================

    /**
     * علاقة السائق بالمستخدم الأساسي (حساب الدخول)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}