<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('driver_details', function (Blueprint $table) {
            $table->id();
            // ربط السائق بجدول المستخدمين الأساسي
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            
            // بيانات السيارة
            $table->string('car_model');
            $table->string('car_number');
            $table->string('car_color');
            
            // بيانات الرخصة والتوثيق (طلب صاحب المشروع)
            $table->string('license_number');
            $table->string('id_card_image')->nullable();   // صورة الهوية
            $table->string('personal_image')->nullable();  // الصورة الشخصية
            
            // --- التحديثات الأمنية والمالية الجديدة ---
            
            // رصيد السائق (المحفظة) - دقة عالية للمبالغ المالية
            $table->decimal('wallet_balance', 10, 2)->default(0.00); 

            // حالة السائق: هل وافقت الإدارة عليه؟ (طلب صاحب المشروع)
            $table->boolean('is_verified')->default(false); 

            // الحالة التشغيلية والموقع (للتتبع وخرائط Google)
            $table->boolean('is_available')->default(false);
            $table->double('current_lat', 10, 8)->nullable();
            $table->double('current_long', 11, 8)->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('driver_details');
    }
}