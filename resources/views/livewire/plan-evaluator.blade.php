<div class="d-flex flex-column min-vh-100 bg-light">

    <!-- Navbar / Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-3 mb-4">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold fs-5" href="#">
                <i class="bi bi-activity text-primary fs-4"></i>
                <span>منصة فحص وتقييم خطط SBRT / SRS</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 fw-medium">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#"><i class="bi bi-speedometer2 me-1"></i> لوحة الفحص</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#aboutModal"><i class="bi bi-info-circle me-1"></i> عن المنصة</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#contactModal"><i class="bi bi-envelope me-1"></i> اتصل بنا</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill small">
                        <i class="bi bi-shield-check me-1"></i> بروتوكول SBRT المعتمد
                    </span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container-fluid px-4 flex-grow-1">

        <!-- Alert Notifications -->
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            <!-- القسم الأيسر: نموذج إدخال البيانات -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-dark text-white p-3 border-0 d-flex justify-content-between align-items-center">
                        <span class="fw-semibold small text-uppercase tracking-wider">
                            <i class="bi bi-sliders me-2 text-info"></i>مدخلات خطة SBRT
                        </span>
                    </div>
                    
                    <div class="card-body p-4 bg-white">
                        <form wire:submit.prevent="calculateAndSave">
                            <!-- معلومات المريض -->
                            <div class="mb-4">
                                <label class="form-label fs-7 fw-bold text-uppercase text-secondary mb-2">بيانات المريض والمعالجة</label>
                                
                                <div class="mb-3">
                                    <label class="form-label small text-dark fw-medium">رقم الملف الطبي (Patient ID)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person-vcard"></i></span>
                                        <input type="text" wire:model.defer="patient_id" class="form-control bg-light border-start-0 @error('patient_id') is-invalid @enderror" placeholder="مثال: PAT-10092">
                                    </div>
                                    @error('patient_id') <div class="invalid-feedback d-block small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small text-dark fw-medium">اسم المريض الكامل</label>
                                    <input type="text" wire:model.defer="patient_name" class="form-control bg-light @error('patient_name') is-invalid @enderror" placeholder="اسم المريض الثلاثي">
                                    @error('patient_name') <div class="invalid-feedback d-block small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div>
                                    <label class="form-label small text-dark fw-medium">تقنية العلاج (Plan Technique)</label>
                                    <input type="text" class="form-control bg-light fw-bold text-primary" value="SBRT / SRS (Stereotactic Radiotherapy)" readonly>
                                </div>
                            </div>

                            <div class="my-4 border-top opacity-50"></div>

                            <!-- المدخلات الفيزيائية -->
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="form-label fs-7 fw-bold text-uppercase text-primary mb-0">القيم الفيزيائية (SBRT DVH Statistics)</label>
                                    <span class="text-muted fs-8">الوحدة: Gy / cc</span>
                                </div>

                                <!-- D-Metrics Group -->
                                <div class="row g-2 mb-3">
                                    <div class="col-4">
                                        <div class="form-floating">
                                            <input type="number" step="0.0001" wire:model.defer="d2_percent" class="form-control form-control-sm @error('d2_percent') is-invalid @enderror" id="d2_inp" placeholder="D2%">
                                            <label for="d2_inp" class="small">D2% (Gy)</label>
                                        </div>
                                        @error('d2_percent') <span class="text-danger fs-8 d-block mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-4">
                                        <div class="form-floating">
                                            <input type="number" step="0.0001" wire:model.defer="d50_percent" class="form-control form-control-sm @error('d50_percent') is-invalid @enderror" id="d50_inp" placeholder="D50%">
                                            <label for="d50_inp" class="small">D50% (Gy)</label>
                                        </div>
                                        @error('d50_percent') <span class="text-danger fs-8 d-block mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-4">
                                        <div class="form-floating">
                                            <input type="number" step="0.0001" wire:model.defer="d98_percent" class="form-control form-control-sm @error('d98_percent') is-invalid @enderror" id="d98_inp" placeholder="D98%">
                                            <label for="d98_inp" class="small">D98% (Gy)</label>
                                        </div>
                                        @error('d98_percent') <span class="text-danger fs-8 d-block mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <!-- V-Metrics Group -->
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="number" step="0.0001" wire:model.defer="v_ptv_total" class="form-control form-control-sm @error('v_ptv_total') is-invalid @enderror" id="vptv_inp" placeholder="V_PTV">
                                            <label for="vptv_inp" class="small">V_PTV_Total (cc)</label>
                                        </div>
                                        @error('v_ptv_total') <span class="text-danger fs-8 d-block mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="number" step="0.0001" wire:model.defer="v100_percent" class="form-control form-control-sm @error('v100_percent') is-invalid @enderror" id="v100_inp" placeholder="V100%">
                                            <label for="v100_inp" class="small">V100% (cc)</label>
                                        </div>
                                        @error('v100_percent') <span class="text-danger fs-8 d-block mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="row g-2 mb-4">
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="number" step="0.0001" wire:model.defer="v95_percent" class="form-control form-control-sm @error('v95_percent') is-invalid @enderror" id="v95_inp" placeholder="V95%">
                                            <label for="v95_inp" class="small">V95% (cc)</label>
                                        </div>
                                        @error('v95_percent') <span class="text-danger fs-8 d-block mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="number" step="0.0001" wire:model.defer="v50_percent" class="form-control form-control-sm @error('v50_percent') is-invalid @enderror" id="v50_inp" placeholder="V50%">
                                            <label for="v50_inp" class="small">V50% (cc)</label>
                                        </div>
                                        @error('v50_percent') <span class="text-danger fs-8 d-block mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <button type="submit" wire:loading.attr="disabled" class="btn btn-primary w-100 fw-bold py-3 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2">
                                <span wire:loading.remove wire:target="calculateAndSave">
                                    <i class="bi bi-cpu me-1"></i> معالجة وحفظ خطة SBRT
                                </span>
                                <span wire:loading wire:target="calculateAndSave">
                                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                    جاري إجراء حسابات SBRT...
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- القسم الأيمن: عرض النتائج والتقييم اللحظي -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark">
                            <i class="bi bi-graph-up-arrow text-primary me-2"></i>مؤشرات SBRT الحسابية (Dosimetric Metrics)
                        </span>
                        <span class="badge bg-light text-muted border">Real-time Calculation</span>
                    </div>
                    
                    <div class="card-body p-4">
                        <!-- Cards Grid -->
                        <div class="row g-3 text-center">
                            <div class="col-md-4">
                                <div class="p-3 rounded-3 bg-light border border-light-subtle h-100 position-relative">
                                    <span class="text-uppercase fs-8 text-muted fw-bold d-block mb-1">Homogeneity (HI)</span>
                                    <div class="fs-3 fw-bolder text-dark mb-1">{{ is_numeric($hi_index) ? number_format($hi_index, 3) : ($hi_index ?? '—') }}</div>
                                    <span class="badge bg-white text-secondary border fs-8">الهدف: &le; 0.20</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded-3 bg-light border border-light-subtle h-100 position-relative">
                                    <span class="text-uppercase fs-8 text-muted fw-bold d-block mb-1">Conformity (CI)</span>
                                    <div class="fs-3 fw-bolder text-dark mb-1">{{ is_numeric($ci_index) ? number_format($ci_index, 3) : ($ci_index ?? '—') }}</div>
                                    <span class="badge bg-white text-secondary border fs-8">الهدف: 0.80 - 1.20</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded-3 bg-light border border-light-subtle h-100 position-relative">
                                    <span class="text-uppercase fs-8 text-muted fw-bold d-block mb-1">Gradient (GI)</span>
                                    <div class="fs-3 fw-bolder text-dark mb-1">{{ is_numeric($gi_index) ? number_format($gi_index, 3) : ($gi_index ?? '—') }}</div>
                                    <span class="badge bg-white text-secondary border fs-8">الهدف: &le; 5.0</span>
                                </div>
                            </div>
                        </div>

                        <div class="my-4 border-top opacity-50"></div>

                        <!-- التقييم والتصنيف النهائي - الألوان الأربعة/الثلاثة للحالات -->
                        <div class="mb-2">
                            <h6 class="fw-bold text-dark mb-3">مصفوفة تقييم خطط SBRT (Decision Support System)</h6>

                            @if(!empty($status_level))
                                @php
                                    $statusClass = method_exists($this, 'getStatusBadgeClass') ? $this->getStatusBadgeClass() : 'primary';
                                    
                                    // استخدام ألوان Hex المباشرة للتحكم التام بلون الأيقونة
                                    $themeConfig = match(true) {
                                        $statusClass === 'success' || str_contains($status_level, 'optimal') || str_contains($status_level, 'green') => [
                                            'bg' => 'bg-success-subtle',
                                            'border' => 'border-success',
                                            'icon' => 'bi-check-circle-fill',
                                            'hex' => '#198754',
                                            'glow' => 'rgba(25, 135, 84, 0.3)',
                                            'badge_bg' => 'bg-success text-white',
                                            'title' => 'مقبولة - Optimal SBRT Plan'
                                        ],
                                        $statusClass === 'warning' || str_contains($status_level, 'variation') || str_contains($status_level, 'yellow') || str_contains($status_level, 'acceptable_with_modification') => [
                                            'bg' => 'bg-warning-subtle',
                                            'border' => 'border-warning',
                                            'icon' => 'bi-exclamation-triangle-fill',
                                            'hex' => '#ffc107',
                                            'glow' => 'rgba(255, 193, 7, 0.4)',
                                            'badge_bg' => 'bg-warning text-dark',
                                            'title' => 'مقبولة مع التعديل / تحذير - Minor Variance'
                                        ],
                                        $statusClass === 'danger' || str_contains($status_level, 'unacceptable') || str_contains($status_level, 'red') => [
                                            'bg' => 'bg-danger-subtle',
                                            'border' => 'border-danger',
                                            'icon' => 'bi-x-circle-fill',
                                            'hex' => '#dc3545',
                                            'glow' => 'rgba(220, 53, 69, 0.3)',
                                            'badge_bg' => 'bg-danger text-white',
                                            'title' => 'مرفوضة - Major Deviation'
                                        ],
                                        default => [
                                            'bg' => 'bg-primary-subtle',
                                            'border' => 'border-primary',
                                            'icon' => 'bi-info-circle-fill',
                                            'hex' => '#0d6efd',
                                            'glow' => 'rgba(13, 110, 253, 0.3)',
                                            'badge_bg' => 'bg-primary text-white',
                                            'title' => 'تم التقييم'
                                        ],
                                    };
                                @endphp

                                <div class="card border border-2 {{ $themeConfig['border'] }} {{ $themeConfig['bg'] }} p-4 rounded-4 shadow-sm position-relative overflow-hidden">
                                    <div class="d-flex align-items-center justify-content-between gap-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <!-- الحاوية الدائرية مع استخدام اللون عبر Style لتجنب أي تعارض -->
                                            <div class="d-flex align-items-center justify-content-center rounded-circle bg-white shadow-sm p-2" style="width: 58px; height: 58px; min-width: 58px; box-shadow: 0 0 18px {{ $themeConfig['glow'] }} !important;">
                                                <i class="bi {{ $themeConfig['icon'] }} fs-2" style="color: {{ $themeConfig['hex'] }} !important;"></i>
                                            </div>
                                            <div>
                                                <span class="badge {{ $themeConfig['badge_bg'] }} px-2 py-1 mb-1 rounded-pill fs-8 fw-bold">
                                                    {{ $themeConfig['title'] }}
                                                </span>
                                                <h5 class="fw-bold mb-0 text-dark">{{ $status_label ?? 'حالة الخطة' }}</h5>
                                            </div>
                                        </div>

                                        @if(isset($ncdi_value))
                                            <div class="text-end">
                                                <span class="text-muted d-block fs-8 fw-bold text-uppercase">مؤشر NCDI</span>
                                                <span class="badge bg-dark text-white px-3 py-2 font-monospace fs-6 shadow-sm">
                                                    {{ is_numeric($ncdi_value) ? number_format($ncdi_value, 3) : $ncdi_value }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mt-3 pt-3 border-top border-secondary border-opacity-10">
                                        <p class="mb-0 text-dark fw-medium fs-6 leading-relaxed">
                                            <i class="bi bi-chat-left-text me-2" style="color: {{ $themeConfig['hex'] }} !important;"></i>
                                            {{ $recommendation ?? '' }}
                                        </p>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-5 border rounded-4 bg-light border-dashed">
                                    <div class="d-flex justify-content-center align-items-center gap-3 mb-3">
                                        <span class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow-sm p-2" style="width: 40px; height: 40px;">
                                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                        </span>
                                        <span class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow-sm p-2" style="width: 40px; height: 40px;">
                                            <i class="bi bi-exclamation-triangle-fill text-warning fs-5"></i>
                                        </span>
                                        <span class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow-sm p-2" style="width: 40px; height: 40px;">
                                            <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                                        </span>
                                    </div>
                                    <span class="text-muted small d-block">قم بإدخال البيانات الفيزيائية واضغط على "معالجة وحفظ خطة SBRT" لتوليد التقييم الطبي.</span>
                                </div>
                            @endif
                        </div>

                        <!-- أزرار الإجراءات التكميلية -->
                        @if(!empty($saved_plan_id))
                            <div class="mt-4 pt-3 border-top d-flex justify-content-end align-items-center gap-2">
                                <a href="{{ route('plan.print', $saved_plan_id) }}" target="_blank" class="btn btn-outline-danger fw-bold px-4 rounded-3 d-inline-flex align-items-center gap-2 shadow-sm">
                                    <i class="bi bi-file-earmark-pdf"></i> استخراج التقرير المعتمد (PDF)
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white-50 py-4 mt-5 border-top border-secondary border-opacity-25">
        <div class="container-fluid px-4">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="mb-0 small">&copy; {{ date('Y') }} منصة تقييم خطط SBRT / SRS. جميع الحقوق محفوظة.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="#" class="text-white-50 text-decoration-none small me-3" data-bs-toggle="modal" data-bs-target="#aboutModal">عن المنصة</a>
                    <a href="#" class="text-white-50 text-decoration-none small me-3" data-bs-toggle="modal" data-bs-target="#contactModal">الدعم الفني</a>
                    <span class="text-muted small">إخلاء مسؤولية: الأداة لمساندة القرار الطبي والفيزيائي</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Modal: عن المنصة -->
    <div class="modal fade" id="aboutModal" tabindex="-1" aria-labelledby="aboutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold" id="aboutModalLabel"><i class="bi bi-info-circle me-2"></i>عن المنصة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-secondary leading-relaxed">
                        منصة متخصصة في حساب وتقييم المؤشرات الفيزيائية لخطط العلاج الإشعاعي الدقيق (SBRT / SRS). تُساعد الفيزيائيين الأطباء وأطباء الأورام الإشعاعية على اتخاذ القرارات وحساب مؤشرات التجانس (HI) والتطابق (CI) والتدرج (GI) ومؤشر NCDI بدقة وسرعة عالية وفق البروتوكولات العالمية المعتمدة.
                    </p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary rounded-3 px-4" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: اتصل بنا -->
    <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header bg-dark text-white border-0">
                    <h5 class="modal-title fw-bold" id="contactModalLabel"><i class="bi bi-envelope me-2"></i>اتصل بنا / الدعم الفني</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4">إذا كان لديك أي استفسار أو بلاغ عن مشكلة فنية، يمكنك التواصل معنا مباشرة:</p>
                    <div class="d-flex align-items-center gap-3 mb-3 p-3 bg-light rounded-3">
                        <i class="bi bi-envelope-at text-primary fs-3"></i>
                        <div>
                            <span class="d-block text-muted fs-8 fw-bold">البريد الإلكتروني</span>
                            <span class="fw-bold text-dark">support@sbrt-evaluator.com</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3">
                        <i class="bi bi-headset text-success fs-3"></i>
                        <div>
                            <span class="d-block text-muted fs-8 fw-bold">الدعم الفني المباشر</span>
                            <span class="fw-bold text-dark">متاح من الإثنين إلى الجمعة (9:00 ص - 5:00 م)</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary rounded-3 px-4" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>

</div>