<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripsTable extends Migration
{
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            
            // الراكب والسائق (ربط بجدول users)
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('set null');

            // بيانات المواقع (نصية وإحداثيات)
            $table->string('pickup_location'); 
            $table->double('pickup_lat', 10, 8);
            $table->double('pickup_long', 11, 8);

            $table->string('dropoff_location');
            $table->double('dropoff_lat', 10, 8);
            $table->double('dropoff_long', 11, 8);

            // بيانات الرحلة الفنية
            $table->double('distance')->nullable(); // المسافة بالكيلومتر (للحساب الدقيق)
            $table->integer('duration')->nullable(); // الوقت المتوقع بالدقائق

            // الحالة المالية (طلب صاحب المشروع)
            $table->decimal('fare', 10, 2)->default(0.00);
            $table->enum('payment_method', ['cash', 'wallet'])->default('cash');
            $table->boolean('is_paid')->default(false);

            // حالة الرحلة (محدثة لتشمل "في الطريق للزبون")
            $table->enum('status', [
                'pending',    // بانتظار سائق
                'accepted',   // قبلها سائق وهو في الطريق للزبون
                'arrived',    // السائق وصل لمكان الزبون
                'ongoing',    // الرحلة بدأت فعلياً
                'completed',  // الرحلة انتهت بنجاح
                'cancelled'   // الرحلة ألغيت
            ])->default('pending');

            // التوقيتات الفعلية (مهمة جداً للتقارير والمطالبات)
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            
            $table->timestamps();
        });
    }
    

    public function down()
    {
        Schema::dropIfExists('trips');
    }
}