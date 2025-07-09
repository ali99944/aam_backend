{{-- resources/views/admin/currencies/_form.blade.php --}}
@csrf
<div class="row">
    <div class="col-md-6 form-group mb-3">
        <label for="name">اسم العملة <span class="text-danger">*</span></label>
        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $currency->name ?? '') }}" required placeholder="مثال: درهم إماراتي">
         @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-3 form-group mb-3">
        <label for="code">الرمز (Code) <span class="text-danger">*</span></label>
        <input type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $currency->code ?? '') }}" required placeholder="مثال: AED" maxlength="5" style="text-transform:uppercase">
         @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-3 form-group mb-3">
        <label for="symbol">الرمز (Symbol)</label>
        <input type="text" id="symbol" name="symbol" class="form-control @error('symbol') is-invalid @enderror" value="{{ old('symbol', $currency->symbol ?? '') }}" placeholder="مثال: د.إ">
        @error('symbol') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
 <div class="form-group mb-3">
    <label for="exchange_rate">سعر الصرف (مقابل العملة الأساسية) <span class="text-danger">*</span></label>
    <input type="number" id="exchange_rate" name="exchange_rate" class="form-control @error('exchange_rate') is-invalid @enderror" value="{{ old('exchange_rate', $currency->exchange_rate ?? '1.000000') }}" required step="0.000001" min="0.000001">
    <small class="form-text text-muted">السعر بالنسبة لعملتك الأساسية (مثال: إذا كانت العملة الأساسية هي الدولار الأمريكي، أدخل سعر الدرهم الإماراتي هنا).</small>
    @error('exchange_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
 <div class="form-group mb-3">
     <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $currency->is_active ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">فعالة</label>
    </div>
</div>
<div class="form-actions mt-4 pt-3 border-top">
    {{-- Adjusted icon placement for RTL --}}
    <button type="submit" class="btn btn-primary"><x-lucide-save class="icon-sm ms-1"/> {{ isset($currency) ? 'تحديث' : 'إنشاء' }}</button>
    <a href="{{ route('admin.locations.currencies.index') }}" class="btn btn-secondary">إلغاء</a>
</div>

{{-- RTL adjustments for form-actions if not global --}}
@push('styles')
<style>
    html[dir="rtl"] .form-actions .btn:first-child {
        margin-left: 0.5rem;
        margin-right: 0;
    }
    html[dir="rtl"] .mr-1 { margin-right: 0 !important; margin-left: 0.25rem !important; }
    html[dir="rtl"] .ms-1 { margin-left: 0 !important; margin-right: 0.25rem !important; }
</style>
@endpush