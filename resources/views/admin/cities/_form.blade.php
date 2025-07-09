{{-- resources/views/admin/cities/_form.blade.php --}}
@csrf

{{-- State Selection --}}
<div class="form-group mb-3">
    <label for="state_id">الولاية/المحافظة <span class="text-danger">*</span></label>
    <select id="state_id" name="state_id" class="form-control select2 @error('state_id') is-invalid @enderror" required data-placeholder="-- اختر ولاية/محافظة --"> {{-- Added placeholder for select2 --}}
        <option value="">-- اختر ولاية/محافظة --</option>
        {{-- Option 1: Grouped Dropdown (assuming $statesGrouped is passed from controller) --}}
        @if(isset($statesGrouped))
            @foreach($statesGrouped as $countryName => $stateList)
                <optgroup label="{{ $countryName }}">
                    @foreach($stateList as $state)
                        <option value="{{ $state->id }}" {{ old('state_id', $city->state_id ?? '') == $state->id ? 'selected' : '' }}>
                            {{ $state->name }}
                        </option>
                    @endforeach
                </optgroup>
            @endforeach
        @elseif(isset($states)) {{-- Option 2: Flat List --}}
            @foreach($states as $id => $name)
                <option value="{{ $id }}" {{ old('state_id', $city->state_id ?? '') == $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        @endif
    </select>
    @error('state_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    {{-- Note: Consider JS to dynamically load states based on a country filter if the list is very long --}}
    {{-- <small class="form-text text-muted">ملاحظة: يمكن تحميل الولايات ديناميكيًا بناءً على فلتر الدولة إذا كانت القائمة طويلة جدًا.</small> --}}
</div>

{{-- City Name --}}
<div class="form-group mb-3">
    <label for="name">اسم المدينة <span class="text-danger">*</span></label>
    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $city->name ?? '') }}" required>
    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Coordinates Row --}}
<div class="row">
     <div class="col-md-6 form-group mb-3">
        <label for="latitude">خط العرض (Latitude)</label>
        <input type="number" id="latitude" name="latitude" class="form-control @error('latitude') is-invalid @enderror"
               value="{{ old('latitude', $city->latitude ?? '') }}" step="0.0000001" min="-90" max="90" placeholder="مثال: 25.2048">
         @error('latitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
     <div class="col-md-6 form-group mb-3">
        <label for="longitude">خط الطول (Longitude)</label>
        <input type="number" id="longitude" name="longitude" class="form-control @error('longitude') is-invalid @enderror"
               value="{{ old('longitude', $city->longitude ?? '') }}" step="0.0000001" min="-180" max="180" placeholder="مثال: 55.2708">
        @error('longitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
     <small class="form-text text-muted px-3 mb-3">اختياري. يستخدم للخرائط أو حسابات المسافة.</small>
</div>


{{-- Active Status --}}
<div class="form-group mb-3">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
               {{ old('is_active', $city->is_active ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">فعالة</label>
         @error('is_active')
            <div class="invalid-feedback d-block">{{ $message }}</div>
         @enderror
         <small class="form-text text-muted d-block">المدن غير الفعالة قد لا تكون متاحة للاختيار في العناوين أو رسوم التوصيل.</small>
    </div>
</div>


<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <x-lucide-save class="icon-sm ms-1"/> {{ isset($city) ? 'تحديث المدينة' : 'إنشاء مدينة' }}
    </button>
    <a href="{{ route('admin.cities.index') }}" class="btn btn-secondary">إلغاء</a>
</div>

{{-- Include Select2 JS/CSS if using it --}}
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
        //                 // dropdownParent: $(this).parent() // Might be needed if inside modal or complex layout
        //             });
        //         });
        //     }
        // });
    </script>
@endpush