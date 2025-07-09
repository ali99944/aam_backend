{{-- resources/views/admin/faqs/_form.blade.php --}}
@csrf

{{-- Category --}}
<div class="form-group mb-3">
    <label for="faq_category_id">الفئة</label>
    <select id="faq_category_id" name="faq_category_id" class="form-control select2 @error('faq_category_id') is-invalid @enderror" data-placeholder="-- غير مصنف --">
        <option value="">-- غير مصنف --</option>
        @foreach($categories as $id => $name)
            <option value="{{ $id }}" {{ old('faq_category_id', $faq->faq_category_id ?? '') == $id ? 'selected' : '' }}>
                {{ $name }}
            </option>
        @endforeach
    </select>
    @error('faq_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    <small><a href="{{ route('admin.faq-categories.create') }}" target="_blank" class="mt-1 d-inline-block">إضافة فئة جديدة</a></small>
</div>

{{-- Question --}}
<div class="form-group mb-3">
    <label for="question">السؤال <span class="text-danger">*</span></label>
    <textarea id="question" name="question" class="form-control @error('question') is-invalid @enderror"
              rows="3" required>{{ old('question', $faq->question ?? '') }}</textarea>
    @error('question') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Answer --}}
<div class="form-group mb-3">
    <label for="answer">الإجابة <span class="text-danger">*</span></label>
    {{-- IMPORTANT: Replace this textarea with a Rich Text Editor (WYSIWYG) --}}
    {{-- Example: <textarea id="answerEditor" name="answer">...</textarea> --}}
    <textarea id="answer" name="answer" class="form-control wysiwyg-editor @error('answer') is-invalid @enderror"
              rows="10" required>{{ old('answer', $faq->answer ?? '') }}</textarea>
    <small class="form-text text-muted">استخدم المحرر لتنسيق الإجابة بالعناوين والقوائم والروابط وما إلى ذلك.</small>
    @error('answer') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Display Order & Status Row --}}
<div class="row">
    <div class="col-md-6 form-group mb-3">
        <label for="display_order">ترتيب العرض</label>
        <input type="number" id="display_order" name="display_order" class="form-control @error('display_order') is-invalid @enderror"
               value="{{ old('display_order', $faq->display_order ?? 0) }}" required min="0" step="1">
         <small class="form-text text-muted">الأرقام الأقل تظهر أولاً ضمن الفئة.</small>
        @error('display_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
     <div class="col-md-6 form-group mb-3 align-self-center"> {{-- Pushed to end in LTR, start in RTL --}}
         <div class="form-check form-switch">
             <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                   {{ old('is_active', $faq->is_active ?? true) ? 'checked' : '' }}>
             <label class="form-check-label" for="is_active">فعال</label>
              @error('is_active')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
             <small class="form-text text-muted d-block">الأسئلة غير الفعالة لن تظهر في الموقع العام.</small>
         </div>
     </div>
</div>

<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <x-lucide-save class="icon-sm ms-1"/> {{ isset($faq) ? 'تحديث السؤال' : 'إنشاء سؤال' }}
    </button>
    <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary">إلغاء</a>
</div>

{{-- Push scripts/styles needed for WYSIWYG editor --}}
@push('styles')
    {{-- <link href="path/to/your/editor.css" rel="stylesheet"> --}}
    <style>
        /* RTL adjustments for form-actions if not global */
        html[dir="rtl"] .form-actions .btn:first-child { margin-left: 0.5rem; margin-right: 0; }
        html[dir="rtl"] .ms-1 { margin-left: 0 !important; margin-right: 0.25rem !important; }
        /* Select2 RTL adjustments */
        /* html[dir="rtl"] .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered { text-align: right; } */
        /* html[dir="rtl"] .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow { left: .5rem; right: auto; } */
    </style>
@endpush
@push('scripts')
    {{-- <script src="path/to/your/editor.js"></script> --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script> --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
    <script>
        // document.addEventListener('DOMContentLoaded', function() {
        //     // Initialize your WYSIWYG editor here
        //     // Example: tinymce.init({ selector: '.wysiwyg-editor', language: 'ar', directionality: 'rtl', ... });
        //     if (typeof $ !== 'undefined' && typeof $.fn.select2 === 'function') {
        //         $('.select2').each(function() {
        //             $(this).select2({
        //                 theme: "bootstrap-5",
        //                 placeholder: $(this).data('placeholder') || "اختر...",
        //                 dir: "rtl" // Ensure Select2 is RTL aware
        //             });
        //         });
        //     }
        // });
    </script>
@endpush