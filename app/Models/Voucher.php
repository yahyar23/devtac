<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 
        'amount', 
        'is_used', 
        'used_by_driver_id', // التأكد من تطابق الاسم مع الـ Migration
        'used_at'
    ];

    /**
     * العلاقة مع المستخدم (الكابتن) الذي استخدم الكود
     */
    public function driver()
    {
        // نربط الكود بجدول المستخدمين عبر الحقل used_by_driver_id
        return $this->belongsTo(User::class, 'used_by_driver_id');
    }
}