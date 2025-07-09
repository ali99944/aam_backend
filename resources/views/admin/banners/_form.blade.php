{{-- resources/views/admin/banners/_form.blade.php --}}
@csrf

<div class="row">
    <div class="col-md-8">
        <div class="form-group mb-3">
            <label for="title">العنوان الرئيسي <span class="text-danger">*</span></label>
            <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
                   value="{{ old('title', $banner->title ?? '') }}" required>
            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="form-group mb-3">
            <label for="description">الوصف / العنوان الفرعي</label>
            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                      rows="3">{{ old('description', $banner->description ?? '') }}</textarea>
            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="row">
            <div class="col-md-6 form-group mb-3">
                <label for="button_text">نص الزر</label>
                <input type="text" id="button_text" name="button_text" class="form-control @error('button_text') is-invalid @enderror"
                       value="{{ old('button_text', $banner->button_text ?? '') }}" placeholder="مثال: اكتشف المزيد">
                @error('button_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6 form-group mb-3">
                <label for="button_url">رابط الزر</label>
                <input type="url" id="button_url" name="button_url" class="form-control @error('button_url') is-invalid @enderror"
                       value="{{ old('button_url', $banner->button_url ?? '') }}" placeholder="مثال: /products/sales">
                @error('button_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group mb-3">
            <label for="image">صورة البانر <span class="text-danger">{{ isset($banner) ? '' : '*' }}</span></label>
            <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
            <small class="form-text text-muted">مطلوبة عند الإنشاء. يفضل مقاس 1920x800 بكسل.</small>
            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
            @if(isset($banner) && $banner->image_url)
                <div class="mt-2 text-center">
                    <img src="{{ $banner->image_url }}" alt="Current Banner" style="max-height: 120px; max-width: 100%; border-radius: 4px;">
                </div>
            @endif
        </div>

        <div class="form-group mb-3">
            <label for="sort_order">ترتيب العرض</label>
            <input type="number" id="sort_order" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror"
                   value="{{ old('sort_order', $banner->sort_order ?? 0) }}" required min="0">
            <small class="form-text text-muted">الأرقام الأقل تظهر أولاً.</small>
            @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="form-group mb-3">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                       {{ old('is_active', $banner->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">فعال</label>
                 <small class="form-text text-muted d-block">البانرات غير الفعالة لن تظهر في الموقع.</small>
            </div>
        </div>
    </div>
</div>

<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <x-lucide-save class="icon-sm mr-1"/> {{ isset($banner) ? 'تحديث البانر' : 'إنشاء البانر' }}
    </button>
    <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">إلغاء</a>
</div>