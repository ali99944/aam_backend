{{-- resources/views/admin/brands/_form.blade.php --}}

@csrf

<div class="row">
    <div class="col-md-8"> {{-- العمود الرئيسي للمحتوى --}}
        {{-- Brand Name --}}
        <div class="form-group mb-3">
            <label for="name">اسم العلامة التجارية <span class="text-danger">*</span></label>
            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $brand->name ?? '') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Brand Image --}}
        <div class="form-group mb-3">
            <label for="image">شعار/صورة العلامة التجارية</label>
            <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/gif,image/webp">
            <small class="form-text text-muted">اختياري. الصيغ المقبولة: JPG, PNG, GIF, WEBP. الحجم الأقصى: 1 ميجابايت.</small>
            @error('image')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            {{-- Display current image --}}
            @if(isset($brand) && $brand->image_url)
                <div class="mt-2">
                    <p class="mb-1"><small>الصورة الحالية:</small></p>
                    <img src="{{ $brand->image_url }}" alt="صورة العلامة التجارية الحالية" style="max-height: 80px; border-radius: 4px; border: 1px solid #eee; padding: 3px;">
                    {{-- اختياري: إضافة خانة اختيار لإزالة الصورة --}}
                    {{--
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image" value="1">
                        <label class="form-check-label" for="remove_image">
                            إزالة الصورة الحالية
                        </label>
                    </div>
                    --}}
                </div>
            @endif
        </div>
    </div>

    <div class="col-md-4"> {{-- العمود الجانبي (لحقول مستقبلية مثل الحالة) --}}
        {{-- إضافة حقول أخرى هنا إذا لزم الأمر، مثل الحالة، علامة مميزة --}}
        <p class="text-muted"><small>يمكن وضع إعدادات إضافية هنا.</small></p>
    </div>
</div>


<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <x-lucide-save class="icon-sm ms-1"/> {{-- ms-1 for RTL --}}
        {{ isset($brand) ? 'تحديث العلامة التجارية' : 'إنشاء علامة تجارية' }}
    </button>
    <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">إلغاء</a>
</div>

{{-- RTL adjustments for form-actions if not global --}}
@push('styles')
<style>
    html[dir="rtl"] .form-actions .btn:first-child {
        margin-left: 0.5rem; /* Space between buttons in RTL */
        margin-right: 0;
    }
    html[dir="rtl"] .mr-1 { margin-right: 0 !important; margin-left: 0.25rem !important; } /* If you use mr-1 directly on icon, this flips it */
    html[dir="rtl"] .ms-1 { margin-left: 0 !important; margin-right: 0.25rem !important; } /* Correct usage for RTL icon on right */
</style>
@endpush