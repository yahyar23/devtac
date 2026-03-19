<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RechargeCard extends Model
{
    use HasFactory;

    /**
     * الحقول المسموح للمستخدم إرسالها فقط عبر الطلبات الخارجية.
     * تم استبعاد amount و is_used لحماية النظام من التلاعب المالي.
     */
    protected $fillable = [
        'code', 
    ];

    /**
     * تحويل أنواع البيانات لضمان دقة الأرقام والتواريخ.
     */
    protected $casts = [
        'amount'  => 'double',    // لضمان وصول المبلغ كرق وليس نص
        'is_used' => 'boolean',   // لسهولة التحقق في الـ if statement
        'used_at' => 'datetime',  // للتعامل مع الوقت باستخدام Carbon
    ];

    /**
     * إخفاء الحقول التقنية عند إرجاع بيانات الكرت كـ JSON.
     */
    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];

    // ========================================================
    // العلاقات (Relationships)
    // ========================================================

    /**
     * علاقة الكارت بالمستخدم (السائق أو الزبون) الذي قام بعملية الشحن.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'used_by');
    }
}