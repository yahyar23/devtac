<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    /**
     * الحقول المسموح بتعبئتها
     * ملاحظة: أضفنا الحقول المالية والتقنية لضمان عمل نظام الحسابات والـ GPS
     */
    protected $fillable = [
        'customer_id', 
        'driver_id', 
        'pickup_location', 
        'pickup_lat', 
        'pickup_long',
        'dropoff_location', 
        'dropoff_lat', 
        'dropoff_long', 
        'distance',      // المسافة المقطوعة
        'duration',      // الوقت المستغرق
        'fare',          // التكلفة
        'payment_method',// كاش أو محفظة
        'is_paid',       // هل تم الدفع؟
        'status', 
        'started_at',    // وقت بدء الرحلة الفعلي
        'ended_at'       // وقت نهاية الرحلة الفعلي
    ];

    /**
     * تحويل البيانات لضمان دقة الأرقام العشرية والتواريخ في الـ API
     */
    protected $casts = [
        'pickup_lat'   => 'double',
        'pickup_long'  => 'double',
        'dropoff_lat'  => 'double',
        'dropoff_long' => 'double',
        'fare'         => 'double',
        'distance'     => 'double',
        'is_paid'      => 'boolean',
        'started_at'   => 'datetime',
        'ended_at'     => 'datetime',
    ];

    // ========================================================
    // العلاقات (Relationships)
    // ========================================================

    /**
     * علاقة الرحلة بالراكب (User)
     */
    public function customer() 
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * علاقة الرحلة بالسائق (User)
     */
    public function driver() 
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}