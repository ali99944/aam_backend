{{-- resources/views/admin/seo/_form.blade.php --}}

@csrf

<div class="row">
    <div class="col-md-8">
        {{-- General Info --}}
        <div class="card mb-4">
            <div class="card-header">معلومات SEO العامة</div>
            <div class="card-body">
                <div class="form-group mb-3">
                    <label for="name">الاسم الإداري <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $seo->name ?? '') }}" required placeholder="مثال: SEO الصفحة الرئيسية، صفحة من نحن">
                     <small class="form-text text-muted">اسم داخلي لتمييز هذا الإعداد.</small>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="key">مفتاح الصفحة <span class="text-danger">*</span></label>
                    <input type="text" id="key" name="key" class="form-control @error('key') is-invalid @enderror"
                           value="{{ old('key', $seo->key ?? '') }}" required placeholder="مثال: home, about.us, contact-page">
                     <small class="form-text text-muted">معرف فريد للصفحة (أحرف صغيرة، أرقام، .، _، -).</small>
                    @error('key') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="title">عنوان ميتا (Meta Title) <span class="text-danger">*</span></label>
                    <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title', $seo->title ?? '') }}" required maxlength="70">
                     <small class="form-text text-muted">يوصى بـ 70 حرفًا كحد أقصى.</small>
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                 <div class="form-group mb-3">
                    <label for="description">وصف ميتا (Meta Description) <span class="text-danger">*</span></label>
                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                              rows="3" required maxlength="160">{{ old('description', $seo->description ?? '') }}</textarea>
                      <small class="form-text text-muted">يوصى بـ 160 حرفًا كحد أقصى.</small>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                 <div class="form-group mb-3">
                    <label for="keywords">الكلمات المفتاحية</label>
                    <input type="text" id="keywords" name="keywords" class="form-control @error('keywords') is-invalid @enderror"
                           value="{{ old('keywords', $seo->keywords ?? '') }}">
                    <small class="form-text text-muted">كلمات مفتاحية مفصولة بفواصل (أقل أهمية في الوقت الحالي).</small>
                    @error('keywords') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

         {{-- Open Graph (Facebook, LinkedIn, etc.) --}}
        <div class="card mb-4">
            <div class="card-header">وسوم Open Graph (للمشاركة على الشبكات الاجتماعية)</div>
            <div class="card-body">
                 <div class="form-group mb-3">
                    <label for="og_title">عنوان OG</label>
                    <input type="text" id="og_title" name="og_title" class="form-control @error('og_title') is-invalid @enderror"
                           value="{{ old('og_title', $seo->og_title ?? '') }}" placeholder="يستخدم عنوان ميتا إذا ترك فارغًا">
                    @error('og_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                 <div class="form-group mb-3">
                    <label for="og_description">وصف OG</label>
                    <textarea id="og_description" name="og_description" class="form-control @error('og_description') is-invalid @enderror"
                              rows="2" placeholder="يستخدم وصف ميتا إذا ترك فارغًا">{{ old('og_description', $seo->og_description ?? '') }}</textarea>
                    @error('og_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                 <div class="form-group mb-3">
                    <label for="og_image">صورة OG</label>
                    <input type="file" id="og_image" name="og_image" class="form-control @error('og_image') is-invalid @enderror" accept="image/*">
                     <small class="form-text text-muted">الحجم الموصى به: 1200x630 بكسل. الحجم الأقصى: 1 ميجابايت.</small>
                    @error('og_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                     @if(isset($seo) && $seo->og_image_url)
                        <div class="mt-2">
                             <p class="mb-1"><small>صورة OG الحالية:</small></p>
                            <img src="{{ $seo->og_image_url }}" alt="صورة OG" style="max-height: 100px;" class="img-thumbnail">
                             <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" name="remove_og_image" id="remove_og_image" value="1">
                                <label class="form-check-label text-danger" for="remove_og_image" style="font-size: 0.8em;">إزالة</label>
                            </div>
                        </div>
                    @endif
                </div>
                 <div class="form-group mb-3">
                    <label for="og_image_alt">النص البديل لصورة OG</label>
                    <input type="text" id="og_image_alt" name="og_image_alt" class="form-control @error('og_image_alt') is-invalid @enderror"
                           value="{{ old('og_image_alt', $seo->og_image_alt ?? '') }}">
                    @error('og_image_alt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                 <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label for="og_locale">لغة OG (OG Locale)</label>
                        <input type="text" id="og_locale" name="og_locale" class="form-control @error('og_locale') is-invalid @enderror"
                               value="{{ old('og_locale', $seo->og_locale ?? 'ar_AE') }}" placeholder="مثال: ar_AE, en_US"> {{-- Changed default to ar_AE --}}
                        @error('og_locale') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label for="og_site_name">اسم موقع OG</label>
                        <input type="text" id="og_site_name" name="og_site_name" class="form-control @error('og_site_name') is-invalid @enderror"
                               value="{{ old('og_site_name', $seo->og_site_name ?? config('app.name')) }}">
                        @error('og_site_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                 </div>
            </div>
        </div>

         {{-- Twitter Card Tags --}}
        <div class="card mb-4">
             <div class="card-header">وسوم بطاقة تويتر</div>
             <div class="card-body">
                  <div class="form-group mb-3">
                    <label for="twitter_title">عنوان تويتر</label>
                    <input type="text" id="twitter_title" name="twitter_title" class="form-control @error('twitter_title') is-invalid @enderror"
                           value="{{ old('twitter_title', $seo->twitter_title ?? '') }}" placeholder="يستخدم عنوان ميتا إذا ترك فارغًا">
                    @error('twitter_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                 <div class="form-group mb-3">
                    <label for="twitter_description">وصف تويتر</label>
                    <textarea id="twitter_description" name="twitter_description" class="form-control @error('twitter_description') is-invalid @enderror"
                              rows="2" placeholder="يستخدم وصف ميتا إذا ترك فارغًا">{{ old('twitter_description', $seo->twitter_description ?? '') }}</textarea>
                    @error('twitter_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-group mb-3">
                    <label for="twitter_image">صورة تويتر</label>
                    <input type="file" id="twitter_image" name="twitter_image" class="form-control @error('twitter_image') is-invalid @enderror" accept="image/*">
                    <small class="form-text text-muted">الحد الأدنى للحجم 144x144، الأقصى 4096x4096، أقل من 1 ميجابايت. تستخدم صورة OG إذا تركت فارغة.</small>
                    @error('twitter_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @if(isset($seo) && $seo->twitter_image_url)
                        <div class="mt-2">
                             <p class="mb-1"><small>صورة تويتر الحالية:</small></p>
                            <img src="{{ $seo->twitter_image_url }}" alt="صورة تويتر" style="max-height: 100px;" class="img-thumbnail">
                             <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" name="remove_twitter_image" id="remove_twitter_image" value="1">
                                <label class="form-check-label text-danger" for="remove_twitter_image" style="font-size: 0.8em;">إزالة</label>
                            </div>
                        </div>
                    @endif
                </div>
                 <div class="form-group mb-3">
                    <label for="twitter_alt">النص البديل لصورة تويتر</label>
                    <input type="text" id="twitter_alt" name="twitter_alt" class="form-control @error('twitter_alt') is-invalid @enderror"
                           value="{{ old('twitter_alt', $seo->twitter_alt ?? '') }}">
                     @error('twitter_alt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
             </div>
        </div>
    </div> {{-- End Left Column --}}

    <div class="col-md-4">
        {{-- Advanced Settings --}}
        <div class="card mb-4">
            <div class="card-header">إعدادات متقدمة</div>
            <div class="card-body">
                <div class="form-group mb-3">
                    <label for="robots_meta">وسم Robots Meta</label>
                    <input type="text" id="robots_meta" name="robots_meta" class="form-control @error('robots_meta') is-invalid @enderror"
                           value="{{ old('robots_meta', $seo->robots_meta ?? 'index, follow') }}" placeholder="مثال: index, follow">
                     <small class="form-text text-muted">يتحكم في زحف وفهرسة محركات البحث.</small>
                    @error('robots_meta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="canonical_url">رابط URL الأساسي (Canonical)</label>
                    <input type="url" id="canonical_url" name="canonical_url" class="form-control @error('canonical_url') is-invalid @enderror"
                           value="{{ old('canonical_url', $seo->canonical_url ?? '') }}" placeholder="اتركه فارغًا للإشارة الذاتية">
                    <small class="form-text text-muted">الرابط المفضل لهذه الصفحة إذا كان المحتوى مكررًا.</small>
                     @error('canonical_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                 <div class="form-group mb-3">
                    <label for="custom_meta_tags">وسوم ميتا مخصصة</label>
                    <textarea id="custom_meta_tags" name="custom_meta_tags" class="form-control @error('custom_meta_tags') is-invalid @enderror"
                              rows="4" placeholder="مثال: <meta name='custom' content='value'>">{{ old('custom_meta_tags', $seo->custom_meta_tags ?? '') }}</textarea>
                     <small class="form-text text-muted">أضف أي وسوم ميتا إضافية هنا (استخدم بحذر).</small>
                    @error('custom_meta_tags') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div> {{-- End Right Column --}}
</div> {{-- End Row --}}

<div class="form-actions mt-0 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <x-lucide-save class="icon-sm ms-1"/> {{ isset($seo) ? 'تحديث إعدادات SEO' : 'إنشاء إعدادات SEO' }}
    </button>
    <a href="{{ route('admin.seo.index') }}" class="btn btn-secondary">إلغاء</a>
</div>

@push('styles')
<style>
    /* RTL adjustments for form-actions if not global */
    html[dir="rtl"] .form-actions .btn:first-child { margin-left: 0.5rem; margin-right: 0; }
    html[dir="rtl"] .ms-1 { margin-left: 0 !important; margin-right: 0.25rem !important; }
    /* For image remove checkbox */
    html[dir="rtl"] .existing-image-item .form-check { /* If this style is needed */
        right: auto;
        left: 10px; /* Assuming this was for LTR's right */
    }
    .img-thumbnail {
        padding: .25rem; background-color: #fff; border: 1px solid #dee2e6;
        border-radius: .25rem; max-width: 100%; height: auto;
    }
</style>
@endpush