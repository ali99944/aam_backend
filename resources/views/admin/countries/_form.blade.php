{{-- resources/views/admin/countries/_form.blade.php --}}
@csrf

<div class="row">
    {{-- Left Column --}}
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">التفاصيل الأساسية</div>
            <div class="card-body">
                {{-- Name --}}
                <div class="form-group mb-3">
                    <label for="name">اسم الدولة <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $country->name ?? '') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- ISO Codes Row --}}
                <div class="row">
                     <div class="col-md-4 form-group mb-3">
                        <label for="iso2">رمز ISO Alpha-2 <span class="text-danger">*</span></label>
                        <input type="text" id="iso2" name="iso2" class="form-control @error('iso2') is-invalid @enderror"
                               value="{{ old('iso2', $country->iso2 ?? '') }}" required maxlength="2" style="text-transform: uppercase;">
                        @error('iso2') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="col-md-4 form-group mb-3">
                        <label for="iso3">رمز ISO Alpha-3</label>
                        <input type="text" id="iso3" name="iso3" class="form-control @error('iso3') is-invalid @enderror"
                               value="{{ old('iso3', $country->iso3 ?? '') }}" maxlength="3" style="text-transform: uppercase;">
                         @error('iso3') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="col-md-4 form-group mb-3">
                        <label for="phone_code">رمز الهاتف الدولي</label>
                        <input type="text" id="phone_code" name="phone_code" class="form-control @error('phone_code') is-invalid @enderror"
                               value="{{ old('phone_code', $country->phone_code ?? '') }}" placeholder="مثال: 971+">
                         @error('phone_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                 {{-- Capital --}}
                 <div class="form-group mb-3">
                    <label for="capital">العاصمة</label>
                    <input type="text" id="capital" name="capital" class="form-control @error('capital') is-invalid @enderror"
                           value="{{ old('capital', $country->capital ?? '') }}">
                    @error('capital') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="card mb-4">
             <div class="card-header">المعلومات الإقليمية والمالية</div>
             <div class="card-body">
                 {{-- Region/Subregion Row --}}
                <div class="row">
                     <div class="col-md-6 form-group mb-3">
                        <label for="region">المنطقة</label>
                        <input type="text" id="region" name="region" class="form-control @error('region') is-invalid @enderror"
                               value="{{ old('region', $country->region ?? '') }}" placeholder="مثال: آسيا">
                         @error('region') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="col-md-6 form-group mb-3">
                        <label for="subregion">المنطقة الفرعية</label>
                        <input type="text" id="subregion" name="subregion" class="form-control @error('subregion') is-invalid @enderror"
                               value="{{ old('subregion', $country->subregion ?? '') }}" placeholder="مثال: غرب آسيا">
                         @error('subregion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                 {{-- Currency & Timezone Row --}}
                 <div class="row">
                      <div class="col-md-6 form-group mb-3">
                        <label for="currency_id">العملة</label>
                        <select id="currency_id" name="currency_id" class="form-control select2 @error('currency_id') is-invalid @enderror" data-placeholder="-- اختر العملة --">
                            <option value="">-- اختر العملة --</option>
                            @foreach($currencies as $id => $name)
                                <option value="{{ $id }}" {{ old('currency_id', $country->currency_id ?? '') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                         @error('currency_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                         {{-- <small><a href="{{ route('admin.currencies.index') }}" target="_blank">إدارة العملات</a></small> --}}
                    </div>
                     <div class="col-md-6 form-group mb-3">
                        <label for="timezone_id">المنطقة الزمنية</label>
                        <select id="timezone_id" name="timezone_id" class="form-control select2 @error('timezone_id') is-invalid @enderror" data-placeholder="-- اختر المنطقة الزمنية --">
                            <option value="">-- اختر المنطقة الزمنية --</option>
                             @foreach($timezones as $id => $name) {{-- Make sure $timezones is passed to the view --}}
                                <option value="{{ $id }}" {{ old('timezone_id', $country->timezone_id ?? '') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                         @error('timezone_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                 </div>
            </div>
        </div>
    </div>

    {{-- Right Column --}}
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">العلم والحالة</div>
            <div class="card-body">
                 {{-- Flag Image --}}
                <div class="form-group mb-3">
                    <label for="flag_image">صورة العلم</label>
                    <input type="file" id="flag_image" name="flag_image" class="form-control @error('flag_image') is-invalid @enderror" accept="image/*">
                     <small class="form-text text-muted">اختياري. يفضل SVG. الحجم الأقصى: 512 كيلوبايت.</small>
                    @error('flag_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @if(isset($country) && $country->flag_image_url)
                        <div class="mt-2 text-center">
                            <img src="{{ $country->flag_image_url }}" alt="العلم الحالي" style="max-height: 60px; border: 1px solid #eee;">
                        </div>
                    @endif
                </div>

                {{-- Active Status --}}
                <div class="form-group mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                               {{ old('is_active', $country->is_active ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">فعالة</label>
                         @error('is_active')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                         @enderror
                         <small class="form-text text-muted d-block">الدول غير الفعالة قد لا تكون متاحة للاختيار في أماكن أخرى.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <x-lucide-save class="icon-sm ms-1"/> {{ isset($country) ? 'تحديث الدولة' : 'إنشاء دولة' }}
    </button>
    <a href="{{ route('admin.countries.index') }}" class="btn btn-secondary">إلغاء</a>
</div>

{{-- Select2 includes if using --}}
@push('styles')
    {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" /> --}}
    <style>
        /* Ensure select2 uses full width if needed and aligns with RTL */
        /* .select2-container { width: 100% !important; } */
        /* html[dir="rtl"] .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered { text-align: right; } */
        /* html[dir="rtl"] .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow { left: 0.5rem; right: auto; } */
    </style>
@endpush
@push('scripts')
    {{-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script> --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
    <script>
        // document.addEventListener('DOMContentLoaded', function() {
        //     if (typeof $ !== 'undefined' && typeof $.fn.select2 === 'function') {
        //         $('.select2').each(function() {
        //             $(this).select2({
        //                 theme: "bootstrap-5",
        //                 placeholder: $(this).data('placeholder') || "اختر...",
        //                 // dropdownParent: $(this).parent() // Useful for modals
        //             });
        //         });
        //     }
        // });
    </script>
@endpush