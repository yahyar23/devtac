<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // الكود السري
        $table->decimal('amount', 10, 2); // قيمة الشحن (مثلاً 5000)
        $table->boolean('is_used')->default(false); // هل استخدم؟
        $table->foreignId('used_by_driver_id')->nullable()->constrained('users'); // من السائق؟
        $table->timestamp('used_at')->nullable(); // متى؟
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
        Schema::dropIfExists('vouchers');
    }
}
