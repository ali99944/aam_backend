{{-- resources/views/admin/testimonials/_form.blade.php --}}
@csrf

<div class="row">
    <div class="col-md-8">
        <div class="form-group mb-3">
            <label for="name">اسم الشخص <span class="text-danger">*</span></label>
            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $testimonial->name ?? '') }}" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="form-group mb-3">
            <label for="title_or_company">المنصب أو الشركة</label>
            <input type="text" id="title_or_company" name="title_or_company" class="form-control @error('title_or_company') is-invalid @enderror"
                   value="{{ old('title_or_company', $testimonial->title_or_company ?? '') }}" placeholder="مثال: مدير تنفيذي، شركة تقنية">
            @error('title_or_company') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="form-group mb-3">
            <label for="quote">نص التقييم <span class="text-danger">*</span></label>
            <textarea id="quote" name="quote" class="form-control @error('quote') is-invalid @enderror"
                      rows="5" required>{{ old('quote', $testimonial->quote ?? '') }}</textarea>
            @error('quote') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group mb-3">
            <label for="avatar">الصورة الشخصية (Avatar)</label>
            <input type="file" id="avatar" name="avatar" class="form-control @error('avatar') is-invalid @enderror" accept="image/*">
            <small class="form-text text-muted">اختياري. الحجم الأقصى: 1 ميجابايت.</small>
            @error('avatar') <div class="invalid-feedback">{{ $message }}</div> @enderror
            @if(isset($testimonial) && $testimonial->avatar_url)
                <div class="mt-2 text-center">
                    <img src="{{ $testimonial->avatar_url }}" alt="Current Avatar" style="max-height: 100px; border-radius: 50%;">
                </div>
            @endif
        </div>
        <div class="form-group mb-3">
            <label for="rating">التقييم (من 1 إلى 5)</label>
            <select name="rating" id="rating" class="form-control @error('rating') is-invalid @enderror">
                <option value="">-- بدون تقييم --</option>
                @for ($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" {{ old('rating', $testimonial->rating ?? '') == $i ? 'selected' : '' }}>
                        {{ $i }} {{ $i > 1 ? 'نجوم' : 'نجمة' }}
                    </option>
                @endfor
            </select>
            @error('rating') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="form-group mb-3">
            <label for="sort_order">ترتيب العرض</label>
            <input type="number" id="sort_order" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror"
                   value="{{ old('sort_order', $testimonial->sort_order ?? 0) }}" required min="0">
            <small class="form-text text-muted">الأرقام الأقل تظهر أولاً.</small>
            @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="form-group mb-3">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                       {{ old('is_active', $testimonial->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">فعال</label>
            </div>
        </div>
    </div>
</div>
<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <x-lucide-save class="icon-sm mr-1"/> {{ isset($testimonial) ? 'تحديث التقييم' : 'إنشاء التقييم' }}
    </button>
    <a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary">إلغاء</a>
</div>