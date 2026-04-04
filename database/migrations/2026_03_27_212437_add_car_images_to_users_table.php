<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCarImagesToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // إضافة الحقول مع السماح بأن تكون فارغة (nullable) لتجنب المشاكل مع البيانات القديمة
            $table->string('img_car_front')->nullable()->after('email'); 
            $table->string('img_car_back')->nullable()->after('img_car_front');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // حذف الحقول في حال تراجعت عن العملية
            $table->dropColumn(['img_car_front', 'img_car_back']);
        });
    }
}
