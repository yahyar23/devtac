<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCarDetalisToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ماركة السيارة (مثلاً: تويوتا)
            $table->string('car_brand')->nullable();
            
            // موديل السيارة (مثلاً: كامري)
            $table->string('car_model')->nullable();
            
            // سنة الصنع (سنة فقط)
            $table->year('car_year')->nullable();
            
            // لون السيارة
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // حذف الحقول في حال التراجع
            $table->dropColumn([
                'car_brand', 
                'car_model', 
                'car_year', 
            ]);
        });
    }
}
