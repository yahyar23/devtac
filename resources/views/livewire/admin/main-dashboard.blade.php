<div>
    <div wire:poll.10s>
        <!-- الهيدر والترحيب -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1 text-dark">
                    <i class="bi bi-activity text-primary me-2"></i>لوحة تحكم تقييم العلاج الإشعاعي (RT Plan Evaluator)
                </h4>
                <p class="text-muted small mb-0">متابعة فورية لخطط الـ SBRT / SRS والجرعات الإشعاعية وحركة الزوار.</p>
            </div>
            <div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill">
                    <i class="bi bi-broadcast me-1"></i> التحديث التلقائي مفعل (10ث)
                </span>
            </div>
        </div>

        <!-- 1. كروت الإحصائيات العامة -->
        <div class="row g-3 mb-4">
            <!-- إجمالي المشاهدات -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold">إجمالي المشاهدات</span>
                            <h3 class="fw-bold my-1 text-info">{{ number_format($stats['total_views'] ?? 0) }}</h3>
                            <small class="text-muted" style="font-size: 0.75rem;">زيارات الموقع الكلية</small>
                        </div>
                        <div class="bg-info bg-opacity-10 text-info p-3 rounded-4">
                            <i class="bi bi-eye fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الزوار الفريدون -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold">الزوار الفريدون</span>
                            <h3 class="fw-bold my-1 text-primary">{{ number_format($stats['unique_visitors'] ?? 0) }}</h3>
                            <small class="text-muted" style="font-size: 0.75rem;">حسب عنوان الـ IP</small>
                        </div>
                        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-4">
                            <i class="bi bi-people fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- زوار اليوم -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold">زوار اليوم</span>
                            <h3 class="fw-bold my-1 text-success">{{ number_format($stats['today_visitors'] ?? 0) }}</h3>
                            <small class="text-muted" style="font-size: 0.75rem;">خلال 24 ساعة الماضية</small>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success p-3 rounded-4">
                            <i class="bi bi-person-check fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- إجمالي خطط العلاج -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold">إجمالي الخطط المفحوصة</span>
                            <h3 class="fw-bold my-1 text-dark">{{ number_format($stats['total_plans'] ?? 0) }}</h3>
                            <small class="text-muted" style="font-size: 0.75rem;">SBRT / SRS / IMRT</small>
                        </div>
                        <div class="bg-dark bg-opacity-10 text-dark p-3 rounded-4">
                            <i class="bi bi-journal-medical fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. حالة الخطط العلاجية وإجراءات سريعة -->
           
        </div>
    </div>
</div>