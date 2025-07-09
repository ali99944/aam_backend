@extends('layouts.admin')
@section('title', 'إعدادات التطبيق - متجر AAM')

@push('styles')
<style>
    /* RTL Tab Styles */
    html[dir="rtl"] .settings-tabs {
        gap: 5px; /* Same gap */
    }
    html[dir="rtl"] .settings-tabs .nav-link {
        /* Adjust padding/margin for RTL icon placement if needed */
    }
    html[dir="rtl"] .settings-tabs .nav-link .mr-1 {
        margin-right: 0 !important;
        margin-left: 0.25rem !important; /* Icon on the right of text */
    }
    html[dir="rtl"] .settings-tabs .nav-link .ms-1 { /* If using ms-1 for icon */
        margin-left: 0 !important;
        margin-right: 0.25rem !important;
    }

    .settings-tabs .nav-link {
        cursor: pointer;
        border: 1px solid transparent;
        display: flex;
        align-items: center;
        gap: 5px;
        border-top-left-radius: .25rem;
        border-top-right-radius: .25rem;
        color: var(--primary-color);
        height: 40px;
        padding: 0.5rem 1rem; /* Added padding for better spacing */
    }
    .settings-tabs .nav-link.active {
        color: #495057;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
        font-weight: 600;
    }
    .tab-content > .tab-pane {
        display: none;
        border: 1px solid #dee2e6;
        border-top: none;
        padding: 1.5rem;
        border-bottom-left-radius: .25rem;
        border-bottom-right-radius: .25rem;
        background-color: #fff;
    }
    .tab-content > .active {
        display: block;
    }
    .form-text { font-size: 0.85em; }
    .img-thumbnail { padding: .25rem; background-color: #fff; border: 1px solid #dee2e6; border-radius: .25rem; max-width: 100%; height: auto; }
    .icon-sm { width: 18px; height: 18px; vertical-align: middle; } /* Adjusted size for better fit */

    /* RTL adjustments for form elements */
    html[dir="rtl"] .form-check-inline { margin-left: 0.5rem; margin-right: 0;}
    html[dir="rtl"] .form-check-label { padding-right: 0.3rem; } /* For switch label spacing */
    html[dir="rtl"] .text-end { text-align: left !important; } /* To align save button to the visual left in RTL */
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>إعدادات التطبيق</h1>
    </div>

    @if (session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger mb-4">{{ session('error') }}</div>
    @endif
     @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <h5 class="alert-heading">تم العثور على أخطاء:</h5>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- Tab Navigation --}}
    <ul class="nav nav-tabs settings-tabs mb-0" id="settingsTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general-content" type="button" role="tab" aria-controls="general-content" aria-selected="true">
                <x-lucide-settings class="icon-sm ms-1"/> عام
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-content" type="button" role="tab" aria-controls="contact-content" aria-selected="false">
                 <x-lucide-contact class="icon-sm ms-1"/> الاتصال والعنوان
             </button>
        </li>
         <li class="nav-item" role="presentation">
            <button class="nav-link" id="social-tab" data-bs-toggle="tab" data-bs-target="#social-content" type="button" role="tab" aria-controls="social-content" aria-selected="false">
                 <x-lucide-share-2 class="icon-sm ms-1"/> روابط التواصل الاجتماعي
            </button>
        </li>
         <li class="nav-item" role="presentation">
            <button class="nav-link" id="localization-tab" data-bs-toggle="tab" data-bs-target="#localization-content" type="button" role="tab" aria-controls="localization-content" aria-selected="false">
                 <x-lucide-languages class="icon-sm ms-1"/> اللغات والتوطين
            </button>
        </li>
         <li class="nav-item" role="presentation">
            <button class="nav-link" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment-content" type="button" role="tab" aria-controls="payment-content" aria-selected="false">
                 <x-lucide-credit-card class="icon-sm ms-1"/> طرق الدفع
             </button>
        </li>
         <li class="nav-item" role="presentation">
            <button class="nav-link" id="delivery-tab" data-bs-toggle="tab" data-bs-target="#delivery-content" type="button" role="tab" aria-controls="delivery-content" aria-selected="false">
                 <x-lucide-truck class="icon-sm ms-1"/> التوصيل والشحن
            </button>
        </li>
    </ul>

    {{-- Form Start --}}
    <form method="POST" action="{{ route('admin.settings.update') }}" class="admin-form" id="settings-form" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Tab Content --}}
        <div class="tab-content" id="settingsTabContent">

            {{-- General Settings Tab --}}
            <div class="tab-pane fade show active" id="general-content" role="tabpanel" aria-labelledby="general-tab">
                <h4 class="mb-4">إعدادات الموقع العامة</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="general_app_name">اسم التطبيق <span class="text-danger">*</span></label>
                            <input type="text" id="general_app_name" name="general.app_name" class="form-control @error('general.app_name') is-invalid @enderror" value="{{ old('general.app_name', $settings['general.app_name'] ?? config('app.name')) }}" required>
                            @error('general.app_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="general_app_url">رابط التطبيق <span class="text-danger">*</span></label>
                            <input type="url" id="general_app_url" name="general.app_url" class="form-control @error('general.app_url') is-invalid @enderror" value="{{ old('general.app_url', $settings['general.app_url'] ?? config('app.url')) }}" required>
                             @error('general.app_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                 <div class="form-group mb-3">
                    <label for="general_admin_email">البريد الإلكتروني للمسؤول <span class="text-danger">*</span></label>
                    <input type="email" id="general_admin_email" name="general.admin_email" class="form-control @error('general.admin_email') is-invalid @enderror" value="{{ old('general.admin_email', $settings['general.admin_email'] ?? '') }}" required>
                    <small class="form-text text-muted">يستخدم لإشعارات النظام.</small>
                    @error('general.admin_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <hr>
                 <div class="row">
                    {{-- Logo Upload --}}
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="general_logo">شعار الموقع</label>
                            <input type="file" id="general_logo" name="general.logo" class="form-control @error('general.logo') is-invalid @enderror" accept="image/*">
                             <small class="form-text text-muted">موصى به: PNG، SVG، أو WEBP مع شفافية. الحجم الأقصى 1 ميجابايت.</small>
                             @error('general.logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            @php $logoPath = $settings['general.logo'] ?? null; @endphp
                            @if($logoPath && Storage::disk('public')->exists($logoPath))
                                <div class="mt-2">
                                    <img src="{{ Storage::disk('public')->url($logoPath) }}" alt="الشعار الحالي" style="max-height: 60px; background-color: #eee; padding: 5px;" class="img-thumbnail">
                                    <div class="form-check form-check-inline ms-2"> {{-- ms-2 for RTL --}}
                                        <input class="form-check-input" type="checkbox" name="remove_logo" id="remove_logo" value="1">
                                        <label class="form-check-label text-danger" for="remove_logo">إزالة</label>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    {{-- Favicon Upload --}}
                     <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="general_favicon">أيقونة الموقع (Favicon)</label>
                            <input type="file" id="general_favicon" name="general.favicon" class="form-control @error('general.favicon') is-invalid @enderror" accept=".ico,.png,.svg">
                             <small class="form-text text-muted">الصيغ: ICO, PNG, SVG. الحجم الموصى به: 32x32 أو أكبر مربع. الحجم الأقصى 256 كيلوبايت.</small>
                             @error('general.favicon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            @php $faviconPath = $settings['general.favicon'] ?? null; @endphp
                             @if($faviconPath && Storage::disk('public')->exists($faviconPath))
                                <div class="mt-2">
                                    <img src="{{ Storage::disk('public')->url($faviconPath) }}" alt="الأيقونة الحالية" style="max-height: 32px;" class="img-thumbnail">
                                     <div class="form-check form-check-inline ms-2">
                                        <input class="form-check-input" type="checkbox" name="remove_favicon" id="remove_favicon" value="1">
                                        <label class="form-check-label text-danger" for="remove_favicon">إزالة</label>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contact Settings Tab --}}
            <div class="tab-pane fade" id="contact-content" role="tabpanel" aria-labelledby="contact-tab">
                 <h4 class="mb-4">معلومات الاتصال</h4>
                 <div class="row">
                     <div class="col-md-6">
                         <div class="form-group mb-3">
                            <label for="contact_phone_primary">الهاتف الأساسي</label>
                            <input type="tel" id="contact_phone_primary" name="contact.phone_primary" class="form-control @error('contact.phone_primary') is-invalid @enderror" value="{{ old('contact.phone_primary', $settings['contact.phone_primary'] ?? '') }}">
                             @error('contact.phone_primary') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                     </div>
                      <div class="col-md-6">
                         <div class="form-group mb-3">
                            <label for="contact_phone_secondary">الهاتف الثانوي</label>
                            <input type="tel" id="contact_phone_secondary" name="contact.phone_secondary" class="form-control @error('contact.phone_secondary') is-invalid @enderror" value="{{ old('contact.phone_secondary', $settings['contact.phone_secondary'] ?? '') }}">
                             @error('contact.phone_secondary') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                     </div>
                 </div>
                 <div class="form-group mb-3">
                    <label for="contact_email_support">بريد الدعم الفني</label>
                    <input type="email" id="contact_email_support" name="contact.email_support" class="form-control @error('contact.email_support') is-invalid @enderror" value="{{ old('contact.email_support', $settings['contact.email_support'] ?? '') }}">
                    @error('contact.email_support') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <hr>
                <h5 class="mb-3">عنوان المتجر</h5>
                <div class="form-group mb-3">
                    <label for="contact_address_street">عنوان الشارع</label>
                    <input type="text" id="contact_address_street" name="contact.address.street" class="form-control @error('contact.address.street') is-invalid @enderror" value="{{ old('contact.address.street', $settings['contact.address.street'] ?? '') }}">
                    @error('contact.address.street') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                 <div class="row">
                     <div class="col-md-4">
                         <div class="form-group mb-3">
                            <label for="contact_address_city">المدينة</label>
                            <input type="text" id="contact_address_city" name="contact.address.city" class="form-control @error('contact.address.city') is-invalid @enderror" value="{{ old('contact.address.city', $settings['contact.address.city'] ?? '') }}">
                             @error('contact.address.city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="contact_address_state">الولاية / المنطقة</label>
                            <input type="text" id="contact_address_state" name="contact.address.state" class="form-control @error('contact.address.state') is-invalid @enderror" value="{{ old('contact.address.state', $settings['contact.address.state'] ?? '') }}">
                            @error('contact.address.state') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="contact_address_zip">الرمز البريدي</label>
                            <input type="text" id="contact_address_zip" name="contact.address.zip" class="form-control @error('contact.address.zip') is-invalid @enderror" value="{{ old('contact.address.zip', $settings['contact.address.zip'] ?? '') }}">
                            @error('contact.address.zip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                     </div>
                 </div>
                 <div class="form-group mb-3">
                    <label for="contact_address_country">الدولة</label>
                    <input type="text" id="contact_address_country" name="contact.address.country" class="form-control @error('contact.address.country') is-invalid @enderror" value="{{ old('contact.address.country', $settings['contact.address.country'] ?? '') }}">
                     @error('contact.address.country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                 </div>
                 <div class="form-group mb-3">
                    <label for="contact_map_url">رابط خرائط جوجل</label>
                    <input type="url" id="contact_map_url" name="contact.map_url" class="form-control @error('contact.map_url') is-invalid @enderror" value="{{ old('contact.map_url', $settings['contact.map_url'] ?? '') }}">
                    @error('contact.map_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                 </div>
            </div>

            {{-- Social Links Tab --}}
            <div class="tab-pane fade" id="social-content" role="tabpanel" aria-labelledby="social-tab">
                <h4 class="mb-4">روابط التواصل الاجتماعي</h4>
                 <p class="text-muted">أدخل الرابط الكامل لملفاتك الشخصية على وسائل التواصل الاجتماعي.</p>
                 {{-- Facebook --}}
                 <div class="form-group mb-3">
                    <label for="social_links_facebook"><x-lucide-facebook class="icon-sm ms-1"/> رابط فيسبوك</label>
                    <input type="url" id="social_links_facebook" name="social.links.facebook" class="form-control @error('social.links.facebook') is-invalid @enderror" value="{{ old('social.links.facebook', $settings['social.links.facebook'] ?? '') }}">
                    @error('social.links.facebook') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                 {{-- Twitter --}}
                 <div class="form-group mb-3">
                    <label for="social_links_twitter"><x-lucide-twitter class="icon-sm ms-1"/> رابط تويتر</label>
                    <input type="url" id="social_links_twitter" name="social.links.twitter" class="form-control @error('social.links.twitter') is-invalid @enderror" value="{{ old('social.links.twitter', $settings['social.links.twitter'] ?? '') }}">
                     @error('social.links.twitter') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                 {{-- Instagram --}}
                <div class="form-group mb-3">
                    <label for="social_links_instagram"><x-lucide-instagram class="icon-sm ms-1"/> رابط انستجرام</label>
                    <input type="url" id="social_links_instagram" name="social.links.instagram" class="form-control @error('social.links.instagram') is-invalid @enderror" value="{{ old('social.links.instagram', $settings['social.links.instagram'] ?? '') }}">
                     @error('social.links.instagram') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                {{-- LinkedIn --}}
                 <div class="form-group mb-3">
                    <label for="social_links_linkedin"><x-lucide-linkedin class="icon-sm ms-1"/> رابط لينكدإن</label>
                    <input type="url" id="social_links_linkedin" name="social.links.linkedin" class="form-control @error('social.links.linkedin') is-invalid @enderror" value="{{ old('social.links.linkedin', $settings['social.links.linkedin'] ?? '') }}">
                    @error('social.links.linkedin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                {{-- Youtube --}}
                 <div class="form-group mb-3">
                    <label for="social_links_youtube"><x-lucide-youtube class="icon-sm ms-1"/> رابط يوتيوب</label>
                    <input type="url" id="social_links_youtube" name="social.links.youtube" class="form-control @error('social.links.youtube') is-invalid @enderror" value="{{ old('social.links.youtube', $settings['social.links.youtube'] ?? '') }}">
                     @error('social.links.youtube') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                 {{-- TikTok --}}
                 <div class="form-group mb-3">
                    <label for="social_links_tiktok"><x-lucide-command class="icon-sm ms-1"/> رابط تيك توك</label> {{-- Replace with actual TikTok icon if available --}}
                    <input type="url" id="social_links_tiktok" name="social.links.tiktok" class="form-control @error('social.links.tiktok') is-invalid @enderror" value="{{ old('social.links.tiktok', $settings['social.links.tiktok'] ?? '') }}">
                     @error('social.links.tiktok') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Localization Tab --}}
            <div class="tab-pane fade" id="localization-content" role="tabpanel" aria-labelledby="localization-tab">
                 <h4 class="mb-4">اللغات والتوقيت</h4>
                 <div class="form-group mb-3">
                    <label for="localization_default_locale">اللغة الافتراضية <span class="text-danger">*</span></label>
                    <select id="localization_default_locale" name="localization.default_locale" class="form-control @error('localization.default_locale') is-invalid @enderror" required>
                         @foreach($availableLocales as $code => $name)
                            <option value="{{ $code }}" {{ old('localization.default_locale', $settings['localization.default_locale'] ?? config('app.locale')) == $code ? 'selected' : '' }}>
                                {{ $name }} ({{ $code }})
                            </option>
                         @endforeach
                    </select>
                     <small class="form-text text-muted">تأكد من وجود ملفات اللغة المقابلة.</small>
                    @error('localization.default_locale') <div class="invalid-feedback">{{ $message }}</div> @enderror
                 </div>

                 <div class="form-group mb-3">
                    <label for="localization_default_timezone">المنطقة الزمنية الافتراضية <span class="text-danger">*</span></label>
                     <select id="localization_default_timezone" name="localization.default_timezone" class="form-control @error('localization.default_timezone') is-invalid @enderror" required>
                         @foreach($timezones as $timezone)
                            <option value="{{ $timezone }}" {{ old('localization.default_timezone', $settings['localization.default_timezone'] ?? config('app.timezone')) == $timezone ? 'selected' : '' }}>
                                {{ $timezone }}
                            </option>
                         @endforeach
                    </select>
                    @error('localization.default_timezone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                 </div>

                <div class="form-group mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="localization_enable_switcher" name="localization.enable_switcher" value="1" {{ old('localization.enable_switcher', $settings['localization.enable_switcher'] ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="localization_enable_switcher">تمكين محول اللغة (واجهة المستخدم)</label>
                    </div>
                    <small class="form-text text-muted">السماح للمستخدمين بتغيير لغة العرض.</small>
                     @error('localization.enable_switcher') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Payment Methods Tab --}}
             <div class="tab-pane fade" id="payment-content" role="tabpanel" aria-labelledby="payment-tab">
                 <h4 class="mb-4">إعدادات بوابات الدفع</h4>

                 {{-- Cash on Delivery (COD) --}}
                 <fieldset class="mb-4 border p-3 rounded">
                    <legend class="w-auto px-2 h6">الدفع عند الاستلام (COD)</legend>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="payment_cod_enabled" name="payment.cod.enabled" value="1" {{ old('payment.cod.enabled', $settings['payment.cod.enabled'] ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="payment_cod_enabled">تمكين الدفع عند الاستلام</label>
                    </div>
                     <small class="form-text text-muted">السماح للعملاء بالدفع عند استلام الطلب.</small>
                 </fieldset>

            </div>

            {{-- Delivery Tab --}}
             <div class="tab-pane fade" id="delivery-content" role="tabpanel" aria-labelledby="delivery-tab">
                 <h4 class="mb-4">الشحن والتوصيل</h4>
                  <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="delivery_default_fee">رسوم التوصيل الافتراضية (دينار)</label>
                             <input type="number" id="delivery_default_fee" name="delivery.default_fee" class="form-control @error('delivery.default_fee') is-invalid @enderror" value="{{ old('delivery.default_fee', $settings['delivery.default_fee'] ?? '') }}" step="0.01" min="0">
                            @error('delivery.default_fee') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    {{-- <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="delivery_free_shipping_threshold">حد الشحن المجاني (دينار)</label>
                             <input type="number" id="delivery_free_shipping_threshold" name="delivery.free_shipping_threshold" class="form-control @error('delivery.free_shipping_threshold') is-invalid @enderror" value="{{ old('delivery.free_shipping_threshold', $settings['delivery.free_shipping_threshold'] ?? '') }}" step="0.01" min="0">
                             <small class="form-text text-muted">الطلبات التي تتجاوز هذا المبلغ تحصل على شحن مجاني (0 لتعطيل الميزة).</small>
                            @error('delivery.free_shipping_threshold') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div> --}}
                 </div>
                 <hr>
                 <p class="text-muted">رسوم التوصيل المخصصة للمدن/المناطق تدار من قسم <a href="{{ route('admin.delivery-fees.index') }}">رسوم التوصيل</a>.</p>
            </div>

        </div> {{-- End Tab Content --}}

        <div class="card mt-4">
            <div class="card-body text-end"> {{-- For RTL, buttons will be on the left --}}
                <button type="submit" class="btn btn-primary btn-lg">
                    <x-lucide-save class="icon-sm ms-1"/> حفظ جميع الإعدادات
                </button>
            </div>
        </div>
    </form> {{-- End Form --}}
@endsection

@push('scripts')
<script>
    // Plain JS Tab Switching Logic
    document.addEventListener('DOMContentLoaded', function () {
        var triggerTabList = [].slice.call(document.querySelectorAll('#settingsTab button.nav-link'));
        var tabContentList = [].slice.call(document.querySelectorAll('.tab-content > .tab-pane'));

        function activateTab(tabLink) {
            // Deactivate all
            triggerTabList.forEach(function (el) {
                el.classList.remove('active');
                el.setAttribute('aria-selected', 'false');
            });
            tabContentList.forEach(function (contentEl) {
                contentEl.classList.remove('show', 'active');
            });

            // Activate clicked tab and relevant content
            tabLink.classList.add('active');
            tabLink.setAttribute('aria-selected', 'true');
            var tabContent = document.querySelector(tabLink.getAttribute('data-bs-target'));
            if (tabContent) {
                tabContent.classList.add('show', 'active');
            }
            localStorage.setItem('activeSettingsTab', tabLink.getAttribute('data-bs-target'));
        }

        triggerTabList.forEach(function (triggerEl) {
            triggerEl.addEventListener('click', function (event) {
                event.preventDefault();
                activateTab(triggerEl);
            });
        });

        var activeTabTarget = localStorage.getItem('activeSettingsTab');
        if (activeTabTarget) {
            var tabToActivate = document.querySelector(`#settingsTab button[data-bs-target="${activeTabTarget}"]`);
            if (tabToActivate) {
                activateTab(tabToActivate); // Use the activateTab function
            } else if (triggerTabList.length > 0) {
                 activateTab(triggerTabList[0]); // Default to first if stored is invalid
            }
        } else if (triggerTabList.length > 0) {
            activateTab(triggerTabList[0]); // Default to first tab
        }
    });
</script>
@endpush