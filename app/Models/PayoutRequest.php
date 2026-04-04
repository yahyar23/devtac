<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayoutRequest extends Model
{
    use HasFactory;

    /**
     * الحقول القابلة للتعبئة لضمان أمان البيانات
     */
    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'notes',
    ];

    /**
     * علاقة الطلب بالمستخدم (صاحب المتجر)
     * تسمح لك بالوصول لبيانات المتجر عبر $payout->user->name
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * (اختياري) "Scope" لتسهيل جلب الطلبات المنتظرة فقط من قاعدة البيانات
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}