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
    
    <!-- Bootstrap Icons & FontAwesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Cairo Font from Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        .sidebar-wrapper {
            width: 260px;
            flex-shrink: 0;
            background-color: #1e293b;
            min-height: 100vh;
        }

        .sidebar-wrapper .nav-link {
            color: #94a3b8;
            padding: 0.8rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 0.2rem;
            transition: all 0.2s;
        }

        .sidebar-wrapper .nav-link:hover, 
        .sidebar-wrapper .nav-link.active {
            color: #ffffff;
            background-color: #334155;
        }

        .main-content {
            flex-grow: 1;
            min-width: 0;
            overflow-x: hidden;
        }

        @media (max-width: 991.98px) {
            .sidebar-wrapper {
                display: none;
            }
        }
    </style>

    @livewireStyles
</head>
<body>

    <div class="d-flex min-vh-100">
        <!-- 1. القائمة الجانبية للشاشات الكبيرة -->
        <aside class="sidebar-wrapper p-3 text-white d-none d-lg-flex flex-column justify-content-between">
            <div>
                <!-- الشعار والعنوان -->
                <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center text-white text-decoration-none mb-4 px-2">
                    <i class="bi bi-activity text-primary fs-3 ms-2"></i>
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
                            <i class="bi bi-speedometer2 ms-2"></i>
                            <span>لوحة التحكم</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                            <i class="fas fa-cog ms-2"></i> 
                            <span>إعدادات الـ جوجل والموقع</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- معلومات المستخدم والخروج -->
            <div>
                <hr class="border-secondary my-3">
                <div class="d-flex align-items-center justify-content-between px-2">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-circle fs-4 ms-2 text-primary"></i>
                        <span class="small fw-bold">{{ auth()->user()->name ?? 'الأدمن' }}</span>
                    </div>
                    @if(Route::has('logout'))
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-link text-danger p-0 me-2" title="تسجيل الخروج">
                                <i class="bi bi-box-arrow-right fs-5"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </aside>

        <!-- 2. القائمة المنزلقة للهواتف (Mobile Offcanvas Sidebar) -->
        <div class="offcanvas offcanvas-start text-white p-3" tabindex="-1" id="mobileSidebar" style="width: 280px; background-color: #1e293b !important;">
            <div class="offcanvas-header p-0 mb-3">
                <h5 class="offcanvas-title text-white fs-6">القائمة الرئيسية</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body p-0 d-flex flex-column justify-content-between h-100">
                <div>
                    <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center text-white text-decoration-none mb-4 px-2">
                        <i class="bi bi-activity text-primary fs-3 ms-2"></i>
                        <div>
                            <h6 class="fw-bold mb-0">RT Plan Evaluator</h6>
                            <small class="text-muted" style="font-size: 0.7rem;">تقييم العلاج الإشعاعي</small>
                        </div>
                    </a>

                    <hr class="border-secondary my-3">

                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i class="bi bi-speedometer2 ms-2"></i>
                                <span>لوحة التحكم</span>
                            </a>
                        </li>
                        
                        <li>
                            <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                                <i class="fas fa-cog ms-2"></i> 
                                <span>إعدادات الـ جوجل والموقع</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div>
                    <hr class="border-secondary my-3">
                    <div class="d-flex align-items-center justify-content-between px-2">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-person-circle fs-4 ms-2 text-primary"></i>
                            <span class="small fw-bold">{{ auth()->user()->name ?? 'الأدمن' }}</span>
                        </div>
                        @if(Route::has('logout'))
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-link text-danger p-0 me-2" title="تسجيل الخروج">
                                    <i class="bi bi-box-arrow-right fs-5"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. المحتوى الرئيسي -->
        <div class="main-content d-flex flex-column">
            <!-- الهيدر العلوي -->
            <header class="bg-white border-bottom py-3 px-3 px-md-4 d-flex justify-content-between align-items-center shadow-sm">
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-light d-lg-none border" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <h5 class="mb-0 fw-bold text-secondary fs-6 fs-md-5">نظام تقييم العلاج الإشعاعي</h5>
                </div>

                <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 px-md-3 py-2 rounded-pill small">
                    <i class="bi bi-check-circle ms-1"></i> <span class="d-none d-sm-inline">النظام يعمل</span>
                </span>
            </header>

            <!-- مكان عرض مكونات Livewire (Slot) -->
            <main class="p-3 p-md-4 flex-grow-1">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @livewireScripts
</body>
</html>