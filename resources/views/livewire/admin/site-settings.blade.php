<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-search-dollar me-2"></i> إعدادات الموقع والـ جوجل</h5>
                </div>
                <div class="card-body">

                    @if (session()->has('message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form wire:submit.prevent="save">
                        
                        <!-- 1. المعلومات الأساسية -->
                        <h6 class="text-secondary border-bottom pb-2 mb-3">1. البيانات الأساسية للموقع</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">اسم الموقع الرئيسي</label>
                                <input type="text" wire:model="site_name" class="form-control">
                                @error('site_name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">الشعار اللفظي (Tagline)</label>
                                <input type="text" wire:model="site_tagline" class="form-control" placeholder="مثال: أفضل منصة لحساب وتخطيط...">
                            </div>
                        </div>

                        <!-- 2. إعدادات SEO الأساسية -->
                        <h6 class="text-secondary border-bottom pb-2 mb-3 mt-4">2. إعدادات محركات البحث (Basic SEO)</h6>
                        
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">عنوان الصفحة (Meta Title) <small class="text-muted">(يفضل أقل من 60 حرف)</small></label>
                            <input type="text" wire:model.live="meta_title" class="form-control" maxlength="60">
                            <div class="d-flex justify-content-between">
                                @error('meta_title') <span class="text-danger small">{{ $message }}</span> @enderror
                                <small class="text-muted ms-auto">{{ Str::length($meta_title) }}/60 حرف</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">وصف الموقع (Meta Description) <small class="text-muted">(الجمال المفضل يظهر في نتائج بحث Google - حتى 160 حرف)</small></label>
                            <textarea wire:model.live="meta_description" class="form-control" rows="3" maxlength="160"></textarea>
                            <div class="d-flex justify-content-between">
                                @error('meta_description') <span class="text-danger small">{{ $message }}</span> @enderror
                                <small class="text-muted ms-auto">{{ Str::length($meta_description) }}/160 حرف</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">الكلمات المفتاحية (Meta Keywords) <small class="text-muted">(مفصولة بفواصل)</small></label>
                            <input type="text" wire:model="meta_keywords" class="form-control" placeholder="مثال: حسابات، تخطيط، حاسبة، العراق">
                        </div>

                        <!-- 3. إعدادات وسائل التواصل (Open Graph / Social Media) -->
                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fas fa-save me-1"></i> حفظ إعدادات الـ جوجل
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>