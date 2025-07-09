{{-- resources/views/admin/expenses/_form.blade.php --}}
@csrf

<div class="row">
    <div class="col-md-6">
         {{-- Category --}}
        <div class="form-group mb-3">
            <label for="expense_category_id">فئة المصروف <span class="text-danger">*</span></label>
            <select id="expense_category_id" name="expense_category_id" class="form-control select2 @error('expense_category_id') is-invalid @enderror" required data-placeholder="-- اختر الفئة --"> {{-- Added select2 and placeholder --}}
                <option value="">-- اختر الفئة --</option>
                @foreach($categories as $id => $name) {{-- Ensure $categories is passed --}}
                    <option value="{{ $id }}" {{ old('expense_category_id', $expense->expense_category_id ?? '') == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            @error('expense_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
             <small><a href="{{ route('admin.expense-categories.create') }}" target="_blank" class="mt-1 d-inline-block">إضافة فئة جديدة</a></small>
        </div>
    </div>
     <div class="col-md-6">
         {{-- Entry Date --}}
        <div class="form-group mb-3">
            <label for="entry_date">تاريخ المصروف <span class="text-danger">*</span></label>
            <input type="date" id="entry_date" name="entry_date" class="form-control @error('entry_date') is-invalid @enderror"
                   value="{{ old('entry_date', isset($expense->entry_date) ? $expense->entry_date->format('Y-m-d') : date('Y-m-d')) }}" required>
            @error('entry_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
         {{-- Amount --}}
        <div class="form-group mb-3">
            <label for="amount">المبلغ (دينار) <span class="text-danger">*</span></label>
            <input type="number" id="amount" name="amount" class="form-control @error('amount') is-invalid @enderror"
                   value="{{ old('amount', $expense->amount ?? '') }}" required step="0.01" min="0.01">
             @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
    <div class="col-md-6">
        {{-- Receipt Image --}}
        <div class="form-group mb-3">
            <label for="receipt_image">الإيصال (اختياري)</label>
            <input type="file" id="receipt_image" name="receipt_image" class="form-control @error('receipt_image') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,application/pdf">
            <small class="form-text text-muted">الصيغ المقبولة: JPG, PNG, PDF. الحجم الأقصى: 2 ميجابايت.</small>
            @error('receipt_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
             @if(isset($expense) && $expense->receipt_image_url)
                <div class="mt-2">
                    <p class="mb-1"><small>الإيصال الحالي:</small></p>
                    @if (Str::endsWith(strtolower($expense->receipt_image ?? ''), '.pdf'))
                         <a href="{{ $expense->receipt_image_url }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                            <x-lucide-file-text class="icon-sm ms-1"/> عرض إيصال PDF
                        </a>
                    @else
                         <a href="{{ $expense->receipt_image_url }}" target="_blank">
                            <img src="{{ $expense->receipt_image_url }}" alt="إيصال" style="max-height: 100px;" class="img-thumbnail">
                         </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>


{{-- Description --}}
<div class="form-group mb-3">
    <label for="description">الوصف / ملاحظات <span class="text-danger">*</span></label>
    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
              rows="4" required>{{ old('description', $expense->description ?? '') }}</textarea>
    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>


<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <x-lucide-save class="icon-sm ms-1"/> {{ isset($expense) ? 'تحديث المصروف' : 'تسجيل المصروف' }}
    </button>
    <a href="{{ route('admin.expenses.index') }}" class="btn btn-secondary">إلغاء</a>
</div>

@push('styles')
<style>
    /* RTL adjustments for form-actions if not global */
    html[dir="rtl"] .form-actions .btn:first-child { margin-left: 0.5rem; margin-right: 0; }
    html[dir="rtl"] .ms-1 { margin-left: 0 !important; margin-right: 0.25rem !important; }
    /* Select2 RTL */
    /* html[dir="rtl"] .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered { text-align: right; } */
    /* html[dir="rtl"] .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow { left: .5rem; right: auto; } */
</style>
@endpush
@push('scripts')
    {{-- Select2 JS if using for category select --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script> --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
    <script>
        // document.addEventListener('DOMContentLoaded', function() {
        //     if (typeof $ !== 'undefined' && typeof $.fn.select2 === 'function') {
        //         $('.select2').each(function() {
        //             $(this).select2({
        //                 theme: "bootstrap-5",
        //                 placeholder: $(this).data('placeholder') || "اختر...",
        //                 dir: "rtl"
        //             });
        //         });
        //     }
        // });
    </script>
@endpush