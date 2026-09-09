<?php

namespace App\http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SiteSettings extends Component
{
    use WithFileUploads;

    // الحقول الأساسية
    public $site_name;
    public $site_tagline;

    // حقول الـ SEO
    public $meta_title;
    public $meta_description;
    public $meta_keywords;
    
    // SEO المتقدم
    public $og_title;
    public $og_description;
    public $og_image;
    public $new_og_image; // لحفظ الصورة الجديدة عند التحديث
    public $canonical_url;
    public $robots_meta = 'index, follow';
    public $google_analytics_id;
    public $google_site_verification;

    public function mount()
    {
        // جلب الإعدادات من قاعدة البيانات عند فتح الصفحة
        $settings = DB::table('site_settings')->first();

        if ($settings) {
            $this->site_name = $settings->site_name;
            $this->site_tagline = $settings->site_tagline;
            $this->meta_title = $settings->meta_title;
            $this->meta_description = $settings->meta_description;
            $this->meta_keywords = $settings->meta_keywords;
            $this->og_title = $settings->og_title;
            $this->og_description = $settings->og_description;
            $this->og_image = $settings->og_image;
            $this->canonical_url = $settings->canonical_url;
            $this->robots_meta = $settings->robots_meta ?? 'index, follow';
            $this->google_analytics_id = $settings->google_analytics_id;
            $this->google_site_verification = $settings->google_site_verification;
        }
    }

    // قواعد التحقق من البيانات وضمان الجودة للـ SEO
    protected function rules()
    {
        return [
            'site_name' => 'required|string|max:100',
            'meta_title' => 'required|string|max:60', // 60 حرف كحد أقصى لـ Google Meta Title
            'meta_description' => 'required|string|max:160', // 160 حرف كحد أقصى لـ Meta Description
            'meta_keywords' => 'nullable|string|max:255',
            'og_title' => 'nullable|string|max:60',
            'og_description' => 'nullable|string|max:160',
            'new_og_image' => 'nullable|image|max:2048', // صورة بحد أقصى 2MB
            'canonical_url' => 'nullable|url',
            'robots_meta' => 'required|string',
            'google_analytics_id' => 'nullable|string|max:50',
            'google_site_verification' => 'nullable|string|max:100',
        ];
    }

public function save()
{
    $this->validate();

    // رفع صورة Open Graph
    $imagePath = $this->og_image;
    if ($this->new_og_image) {
        $imagePath = $this->new_og_image->store('seo', 'public');
    }

    // دالة مساعدة محليّة لإزالة الفراغات المكررة والزائدة (دعم لجميع إصدارات لارافيل)
    $cleanString = function ($value) {
        return $value ? trim(preg_replace('/\s+/', ' ', $value)) : null;
    };

    // تطبيق تنظيف النصوص لتوافق الـ SEO
    $data = [
        'site_name' => $cleanString($this->site_name),
        'site_tagline' => $cleanString($this->site_tagline),
        'meta_title' => Str::limit($cleanString($this->meta_title), 60, ''),
        'meta_description' => Str::limit($cleanString($this->meta_description), 160, ''),
        'meta_keywords' => $cleanString($this->meta_keywords),
        'og_title' => $cleanString($this->og_title ?: $this->meta_title),
        'og_description' => $cleanString($this->og_description ?: $this->meta_description),
        'og_image' => $imagePath,
        'canonical_url' => trim($this->canonical_url),
        'robots_meta' => $this->robots_meta,
        'google_analytics_id' => trim($this->google_analytics_id),
        'google_site_verification' => trim($this->google_site_verification),
        'updated_at' => now(),
    ];

    // تحديث أو إنشاء السجل
    DB::table('site_settings')->updateOrInsert(['id' => 1], $data);

    session()->flash('message', 'تم حفظ إعدادات الـ SEO والموقع بنجاح!');
}
    public function render()
    {
        return view('livewire.admin.site-settings')->layout('layouts.app');
    }
}