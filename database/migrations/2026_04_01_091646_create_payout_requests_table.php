<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payout_requests', function (Blueprint $table) {
            $table->id();
            // ربط الطلب بصاحب المتجر (المستخدم)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // المبلغ المطلوب سحبه
            $table->decimal('amount', 15, 2);
            
            // حالة الطلب: pending (قيد الانتظار), paid (تم الدفع), cancelled (ملغي)
            $table->string('status')->default('pending');
            
            // ملاحظات إضافية (مثل رقم الحوالة أو اسم المندوب الذي سلم المبلغ)
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payout_requests');
    }
};