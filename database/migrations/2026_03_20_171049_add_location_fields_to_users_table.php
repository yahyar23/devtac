<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
{
    Schema::table('users', function (Blueprint $table) {
        // إضافة الحقول مع جعلها nullable لكي لا تسبب خطأ للمستخدمين القدامى
        $table->decimal('lat', 10, 8)->nullable()->after('password');
        $table->decimal('lng', 11, 8)->nullable()->after('lat');
        $table->float('heading')->default(0)->after('lng');
        $table->timestamp('last_location_update')->nullable()->after('heading');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        // حذف الحقول في حال أردنا التراجع (Rollback)
        $table->dropColumn(['lat', 'lng', 'heading', 'last_location_update']);
    });
}
}
