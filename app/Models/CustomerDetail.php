<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerDetail extends Model
{
    use HasFactory;

    /**
     * الحقول المسموح بتعبئتها.
     * ملاحظة أمنية: وضعنا wallet_balance هنا ولكننا سنتحكم بتحديثه 
     * من خلال Controller الشحن فقط لضمان الأمان.
     */
    protected $fillable = [
        'user_id', 
        'wallet_balance', 
        'rating',
        'last_lat', 
        'last_long', 
        'total_trips'
    ];

    /**
     * تحويل أنواع البيانات لضمان وصولها لتطبيق الموبايل كأرقام وليس نصوص.
     */
    protected $casts = [
        'wallet_balance' => 'double',
        'rating'         => 'double',
        'last_lat'       => 'double',
        'last_long'      => 'double',
        'total_trips'    => 'integer',
    ];

    /**
     * إخفاء حقول الوقت التقنية لتقليل حجم بيانات الـ JSON.
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    // ========================================================
    // العلاقات (Relationships)
    // ========================================================

    /**
     * علاقة الزبون بالحساب الأساسي في جدول المستخدمين.
     */
    public function user() 
    {
        return $this->belongsTo(User::class);
    }
}