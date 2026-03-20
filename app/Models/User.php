<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * الحقول المسموح بتعبئتها (تم دمجها في مصفوفة واحدة)
     */
  
    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'role',
        'status',
        'avatar',    // أضفناه لعرض الصورة في التطبيق
        'fcm_token', // أضفناه للإشعارات
        'otp_code',  //  للتحقق من الهاتف
    'img_personal', // أضف هذا
    'img_id_front', // أضف هذا
    'img_id_back',  // أضف هذا
    'car_color',    // أضف هذا
    'car_plate',
    
        ];

    /**
     * الحقول المخفية عند إرسال البيانات للـ API (للأمان)
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp_code', // لا نريد إرسال رمز التحقق في الـ JSON للعامة
    ];

    /**
     * تحويل البيانات لتسهيل التعامل معها في Flutter
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // ========================================================
    // العلاقات (Relationships)
    // ========================================================

    /**
     * علاقة المستخدم بتفاصيل السائق (إذا كان نوع الحساب سائق)
     */
    public function driverDetail() 
    {
        return $this->hasOne(DriverDetail::class);
    }

    /**
     * علاقة المستخدم بتفاصيل الزبون (إذا كان نوع الحساب زبون)
     */
    public function customerDetail() 
    {
        return $this->hasOne(CustomerDetail::class);
    }

    /**
     * علاقة المستخدم بالرحلات التي طلبها (كـ زبون)
     */
    public function tripsAsCustomer()
    {
        return $this->hasMany(Trip::class, 'customer_id');
    }

    /**
     * علاقة المستخدم بالرحلات التي نفذها (كـ سائق)
     */
    public function tripsAsDriver()
    {
        return $this->hasMany(Trip::class, 'driver_id');
    }
    public function username()
{
    return 'phone';
}

// إذا كان المستخدم زبوناً
public function customerTrips() {
    return $this->hasMany(Trip::class, 'customer_id');
}

// إذا كان المستخدم سائقاً
public function driverTrips() {
    return $this->hasMany(Trip::class, 'driver_id');
}
}