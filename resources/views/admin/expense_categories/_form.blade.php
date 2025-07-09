{{-- resources/views/admin/expense_categories/_form.blade.php --}}
@csrf

<div class="form-group mb-3">
    <label for="name">اسم الفئة <span class="text-danger">*</span></label>
    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $expenseCategory->name ?? '') }}" required>
    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="form-group mb-3">
    <label for="description">الوصف</label>
    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
              rows="3">{{ old('description', $expenseCategory->description ?? '') }}</textarea>
    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="row">
    <div class="col-md-6 form-group mb-3">
        <label for="display_order">ترتيب العرض</label>
        <input type="number" id="display_order" name="display_order" class="form-control @error('display_order') is-invalid @enderror"
               value="{{ old('display_order', $expenseCategory->display_order ?? 0) }}" required min="0" step="1">
         <small class="form-text text-muted">الأرقام الأقل تظهر أولاً.</small>
        @error('display_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
     <div class="col-md-6 form-group mb-3 align-self-center">
         <div class="form-check form-switch">
             <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                   {{ old('is_active', $expenseCategory->is_active ?? true) ? 'checked' : '' }}>
             <label class="form-check-label" for="is_active">فعالة</label>
              @error('is_active')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
             <small class="form-text text-muted d-block">الفئات غير الفعالة لن تكون قابلة للاختيار عند إضافة مصروفات جديدة.</small>
         </div>
     </div>
</div>


<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <x-lucide-save class="icon-sm ms-1"/> {{ isset($expenseCategory) ? 'تحديث الفئة' : 'إنشاء فئة' }}
    </button>
    <a href="{{ route('admin.expense-categories.index') }}" class="btn btn-secondary">إلغاء</a>
</div>

@push('styles')
<style>
    /* RTL adjustments for form-actions if not global */
    html[dir="rtl"] .form-actions .btn:first-child { margin-left: 0.5rem; margin-right: 0; }
    html[dir="rtl"] .ms-1 { margin-left: 0 !important; margin-right: 0.25rem !important; }
</style>
@endpush