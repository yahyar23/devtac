<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->decimal('balance', 12, 2)->default(0.00);
            // رقم الهاتف: أهم حقل في تطبيقات التكسي، أضفنا له index لسرعة تسجيل الدخول
            $table->string('phone')->unique()->index(); 
            
            $table->string('email')->unique()->nullable(); 
            $table->timestamp('email_verified_at')->nullable();
            
            // حقل لتخزين رمز OTP (اختياري ولكنه احترافي للتحقق من الهاتف لاحقاً)
            $table->string('otp_code')->nullable();

            $table->string('password'); 
            
            // النوع: أضفنا 'admin' و 'driver' و 'customer'
            $table->enum('role', ['admin', 'driver', 'customer'])->default('customer');
            
            // الحالة: مهمة جداً لطلب الزبون (الموافقة على السائقين)
            $table->enum('status', ['active', 'pending', 'blocked'])->default('pending');

            // حقل الصورة الشخصية السريع (لعرضها في الـ Sidebar في Flutter)
            $table->string('avatar')->nullable();

            // حقل للـ Firebase Token (ضروري جداً لإرسال الإشعارات للموبايل لاحقاً)
            $table->text('fcm_token')->nullable();
            
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}