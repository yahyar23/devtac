<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_details', function (Blueprint $table) {
            $table->id();
            
            // الربط مع جدول المستخدمين الأساسي
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            
            // 1. المحفظة المالية: للتعامل مع الرصيد المشحون أو المسترد
            $table->decimal('wallet_balance', 10, 2)->default(0.00);
            
            // 2. التقييم: يبدأ بـ 5.0 نقاط افتراضياً
            $table->float('rating', 3, 2)->default(5.00);

            // 3. الموقع الأخير (إضافي): لتسريع تحميل الخريطة في الفلاتر عند فتح التطبيق
            $table->double('last_lat', 10, 8)->nullable();
            $table->double('last_long', 11, 8)->nullable();

            // 4. إحصائيات بسيطة: تفيد في نظام الولاء أو العروض مستقبلاً
            $table->integer('total_trips')->default(0);
            
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
        Schema::dropIfExists('customer_details');
    }
}