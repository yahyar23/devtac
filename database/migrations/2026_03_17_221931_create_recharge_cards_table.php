<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRechargeCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recharge_cards', function (Blueprint $table) {
            $table->id();
            
            // 1. رمز الشحن: فريد ومفهرس لسرعة البحث العالية في الـ API
            $table->string('code')->unique()->index(); 
            
            // 2. قيمة الرصيد: دقة عالية (حتى 10 أرقام منها 2 بعد الفاصلة)
            $table->decimal('amount', 10, 2); 
            
            // 3. حالة الكارت: افتراضياً غير مستخدم
            $table->boolean('is_used')->default(false);
            
            // 4. المستفيد: السائق (User) الذي استخدم الكود
            // نستخدم set null لكي لا نفقد بيانات السجل المالي إذا حُذف حساب السائق
            $table->foreignId('used_by')->nullable()->constrained('users')->onDelete('set null');
            
            // 5. توقيت الاستخدام الفعلي: ضروري لتقارير الإدارة
            $table->timestamp('used_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recharge_cards');
    }
}