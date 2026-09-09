    @php
    $seo = DB::table('site_settings')->first();
@endphp

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <!-- SEO Meta Tags -->
    <title>{{ $seo->meta_title ?? config('app.name') }}</title>
    <meta name="description" content="{{ $seo->meta_description ?? '' }}">
    <meta name="keywords" content="{{ $seo->meta_keywords ?? '' }}">
    <meta name="robots" content="{{ $seo->robots_meta ?? 'index, follow' }}">
    
    @if(!empty($seo->canonical_url))
        <link rel="canonical" href="{{ $seo->canonical_url }}" />
    @endif

    <!-- Open Graph / Social Media Meta Tags -->
    <meta property="og:title" content="{{ $seo->og_title ?? $seo->meta_title ?? '' }}" />
    <meta property="og:description" content="{{ $seo->og_description ?? $seo->meta_description ?? '' }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />
    @if(!empty($seo->og_image))
        <meta property="og:image" content="{{ asset('storage/'.$seo->og_image) }}" />
    @endif

    <!-- Google Verification & Analytics -->
    @if(!empty($seo->google_site_verification))
        <meta name="google-site-verification" content="{{ $seo->google_site_verification }}" />
    @endif

    @if(!empty($seo->google_analytics_id))
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $seo->google_analytics_id }}"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());
          gtag('config', '{{ $seo->google_analytics_id }}');
        </script>
    @endif

    <!-- Bootstrap 5 RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Cairo Font from Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            min-width: 260px;
            max-width: 260px;
            min-height: 100vh;
            background-color: #1e293b;
        }
        .sidebar .nav-link {
            color: #94a3b8;
            padding: 0.8rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 0.2rem;
            transition: all 0.2s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #ffffff;
            background-color: #334155;
        }
        .main-content {
            width: 100%;
            overflow-x: hidden;
        }
    </style>

    @livewireStyles
</head>
<body>

    <div class="d-flex">
        
        {{-- عرض السايدبار فقط في صفحات لوحة التحكم، وإخفاؤه في الصفحة الرئيسية '/' --}}
        @if(!request()->is('/'))
        <!-- القائمة الجانبية (Sidebar) -->
        <aside class="sidebar p-3 text-white d-flex flex-column justify-content-between">
            <div>
                <!-- الشعار والعنوان -->
                <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center text-white text-decoration-none mb-4 px-2">
                    <i class="bi bi-activity text-primary fs-3 me-2"></i>
                    <div>
                        <h6 class="fw-bold mb-0">RT Plan Evaluator</h6>
                        <small class="text-muted" style="font-size: 0.7rem;">تقييم العلاج الإشعاعي</small>
                    </div>
                </a>

                <hr class="border-secondary my-3">

                <!-- عناصر القائمة -->
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2 me-2"></i>
                            <span>لوحة التحكم</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="nav-link">
                            <i class="bi bi-file-earmark-medical me-2"></i>
                            <span>خطط المرضى (Plans)</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="nav-link">
                            <i class="bi bi-sliders me-2"></i>
                            <span>معايير الجرعات (Constraints)</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="nav-link">
                            <i class="bi bi-bar-chart-line me-2"></i>
                            <span>منحنيات DVH</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="nav-link">
                            <i class="bi bi-people me-2"></i>
                            <span>إدارة المستخدمين</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- معلومات المستخدم والخروج -->
            <div>
                <hr class="border-secondary my-3">
                <div class="d-flex align-items-center justify-content-between px-2">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-circle fs-4 me-2 text-primary"></i>
                        <span class="small fw-bold">{{ auth()->user()->name ?? 'الأدمن' }}</span>
                    </div>
                    @if(Route::has('logout'))
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-link text-danger p-0 ms-2" title="تسجيل الخروج">
                                <i class="bi bi-box-arrow-right fs-5"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </aside>
        @endif

        <!-- المحتوى الرئيسي (Main Content) -->
        <div class="main-content d-flex flex-column">
            <!-- الهيدر العلوي -->
            <header class="bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="bi bi-activity text-primary fs-3 me-2"></i>
                    <h5 class="mb-0 fw-bold text-secondary">{{ $seo->meta_title ?? config('app.name') }}</h5>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-3 py-2 rounded-pill">
                        <i class="bi bi-check-circle me-1"></i> النظام يعمل
                    </span>

                    {{-- أزرار التنقل حسب مسار الصفحة وحالة تسجيل الدخول --}}
                    @if(request()->is('/'))
                        @auth
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary btn-sm ms-2">
                                <i class="bi bi-speedometer2 me-1"></i> لوحة التحكم
                            </a>
                        @else
                            @if(Route::has('login'))
                                <a href="{{ route('login') }}" class="btn btn-primary btn-sm ms-2">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> دخول المسؤولين
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </header>

            <!-- مكان عرض مكونات Livewire (Slot) -->
            <main class="p-4 flex-grow-1">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @livewireScripts
</body>
</html>