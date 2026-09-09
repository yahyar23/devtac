<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_plans', function (Blueprint $table) {
            $table->id();
            // 1. معلومات المريض والخطة (Patient & Plan Info)
            $table->string('patient_id')->comment('رقم هوية المريض أو الملف الطبي');
            $table->string('patient_name')->nullable()->comment('اسم المريض');
            $table->string('plan_type')->default('Standard')->comment('نوع الخطة: Standard أو SRS/SBRT');

            // 2. المدخلات الفيزيائية (Physical Inputs)
            // نستخدم decimal لضمان الدقة العالية في الأرقام العشرية
            $table->decimal('d2_percent', 8, 4)->comment('D2%');
            $table->decimal('d50_percent', 8, 4)->comment('D50%');
            $table->decimal('d98_percent', 8, 4)->comment('D98%');
            $table->decimal('v_ptv_total', 8, 4)->comment('V_PTV_Total');
            $table->decimal('v100_percent', 8, 4)->comment('V100%');
            $table->decimal('v95_percent', 8, 4)->nullable()->comment('V95%');
            $table->decimal('v50_percent', 8, 4)->comment('V50%');

            // 3. المؤشرات المحسوبة (Calculated Metrics - Phase 2)
            $table->decimal('hi_index', 8, 4)->nullable()->comment('Homogeneity Index (HI)');
            $table->decimal('ci_index', 8, 4)->nullable()->comment('Conformity Index (CI)');
            $table->decimal('gi_index', 8, 4)->nullable()->comment('Gradient Index (GI)');

            // 4. مؤشر التقييم النهائي (Stage 2 NCDI)
            $table->decimal('ncdi_value', 8, 4)->nullable()->comment('NCDI = HI * CI * GI');

            // 5. حالة الفحص والتقييم (Filtration & Stratification Status)
            $table->boolean('passed_gatekeeper')->default(false)->comment('هل اجتاز فحص المرحلة الأولى Stage 1');
            $table->enum('status_level', ['green_light', 'yellow_light', 'red_flag', 'system_halt'])
                  ->default('system_halt')
                  ->comment('تصنيف النتيجة النهائي');
            
            $table->string('status_label')->nullable()->comment('وصف النتيجة: Excellent Plan, Suboptimal, etc.');
            $table->text('recommendation')->nullable()->comment('التوصية الطبية والخطوة التالية');
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
        Schema::dropIfExists('patient_plans');
    }
}
