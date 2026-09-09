<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            
            // الأساسيات
            $table->string('site_name')->default('اسم الموقع');
            $table->string('site_tagline')->nullable(); // الشعار اللفظي
            
            // SEO الأساسي
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable(); // الكلمات المفتاحية
            
            // SEO المتقدم وتواصل اجتماعي (Open Graph & Twitter Cards)
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable(); // صورة المشاركة على شبكات التواصل
            $table->string('canonical_url')->nullable(); // الرابط المباشر الاصلي
            $table->text('robots_meta')->default('index, follow'); // أومر عناكب البحث
            $table->text('google_analytics_id')->nullable(); // معرف Google Analytics (G-XXXXX)
            $table->text('google_site_verification')->nullable(); // كود إثبات ملكية Google Search Console
            
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
        Schema::dropIfExists('site_settings');
    }
}
