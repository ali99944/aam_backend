{{-- resources/views/admin/faqs/_form.blade.php --}}
@csrf

{{-- Category --}}
<div class="form-group mb-3">
    <label for="faq_category_id">Category</label>
    <select id="faq_category_id" name="faq_category_id" class="form-control select2 @error('faq_category_id') is-invalid @enderror">
        <option value="">-- Uncategorized --</option>
        @foreach($categories as $id => $name)
            <option value="{{ $id }}" {{ old('faq_category_id', $faq->faq_category_id ?? '') == $id ? 'selected' : '' }}>
                {{ $name }}
            </option>
        @endforeach
    </select>
    @error('faq_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    <small><a href="{{ route('admin.faq-categories.create') }}" target="_blank">Add New Category</a></small>
</div>

{{-- Question --}}
<div class="form-group mb-3">
    <label for="question">Question <span class="text-danger">*</span></label>
    <textarea id="question" name="question" class="form-control @error('question') is-invalid @enderror"
              rows="3" required>{{ old('question', $faq->question ?? '') }}</textarea>
    @error('question') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Answer --}}
<div class="form-group mb-3">
    <label for="answer">Answer <span class="text-danger">*</span></label>
    {{-- IMPORTANT: Replace this textarea with a Rich Text Editor (WYSIWYG) --}}
    {{-- Example: <textarea id="answerEditor" name="answer">...</textarea> --}}
    <textarea id="answer" name="answer" class="form-control wysiwyg-editor @error('answer') is-invalid @enderror"
              rows="10" required>{{ old('answer', $faq->answer ?? '') }}</textarea>
    <small class="text-muted">Use the editor to format the answer with headings, lists, links, etc.</small>
    @error('answer') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Display Order & Status Row --}}
<div class="row">
    <div class="col-md-6 form-group mb-3">
        <label for="display_order">Display Order</label>
        <input type="number" id="display_order" name="display_order" class="form-control @error('display_order') is-invalid @enderror"
               value="{{ old('display_order', $faq->display_order ?? 0) }}" required min="0" step="1">
         <small class="form-text text-muted">Lower numbers appear first within the category.</small>
        @error('display_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
     <div class="col-md-6 form-group mb-3 align-self-center">
         <div class="form-check form-switch">
             <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                   {{ old('is_active', $faq->is_active ?? true) ? 'checked' : '' }}>
             <label class="form-check-label" for="is_active">Is Active</label>
              @error('is_active')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
             <small class="form-text text-muted d-block">Inactive FAQs won't appear on the public site.</small>
         </div>
     </div>
</div>

<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <x-lucide-save class="icon-sm mr-1"/> {{ isset($faq) ? 'Update FAQ' : 'Create FAQ' }}
    </button>
    <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary">Cancel</a>
</div>

{{-- Push scripts/styles needed for WYSIWYG editor --}}
@push('styles')
    {{-- <link href="path/to/your/editor.css" rel="stylesheet"> --}}
@endpush
@push('scripts')
    {{-- <script src="path/to/your/editor.js"></script> --}}
    <script>
        // document.addEventListener('DOMContentLoaded', function() {
        //     // Initialize your WYSIWYG editor here
        //     // Example: tinymce.init({ selector: '.wysiwyg-editor', ... });
        // });
    </script>
@endpush