<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeliveryFieldsToTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
{
    Schema::table('trips', function (Blueprint $table) {
        // إضافة الحقول بعد حقل user_id لترتيب الجدول
        $table->string('receiver_name')->nullable()->after('customer_id');
        $table->string('receiver_phone')->nullable()->after('receiver_name');
        $table->string('item_type')->nullable()->after('receiver_phone');
        $table->decimal('item_price', 10, 2)->default(0)->after('item_type');
        $table->integer('items_count')->default(1)->after('item_type'); // عدد القطع
    });
}

public function down()
{
    Schema::table('trips', function (Blueprint $table) {
        // هذا الكود للتراجع عن التعديلات إذا لزم الأمر
        $table->dropColumn(['receiver_name', 'receiver_phone', 'item_type', 'item_price','items_count']);
    });
}
}
