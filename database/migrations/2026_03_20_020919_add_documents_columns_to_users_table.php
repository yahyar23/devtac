<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDocumentsColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('users', function (Blueprint $table) {
        // إضافة الحقول مع جعلها قابلة لأن تكون فارغة (nullable)
        // لكي لا يحدث خطأ مع البيانات القديمة التي لا تملك هذه الصور
        $table->string('img_personal')->nullable()->after('password');
        $table->string('img_id_front')->nullable()->after('img_personal');
        $table->string('img_id_back')->nullable()->after('img_id_front');
        $table->string('car_color')->nullable()->after('img_id_back');
        $table->string('car_plate')->nullable()->after('car_color');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        // كود التراجع في حال أردت حذف الحقول مستقبلاً
        $table->dropColumn(['img_personal', 'img_id_front', 'img_id_back', 'car_color', 'car_plate']);
    });
}
}
