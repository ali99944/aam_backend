{{-- resources/views/admin/faq_categories/_form.blade.php --}}
@csrf

<div class="form-group mb-3">
    <label for="name">Category Name <span class="text-danger">*</span></label>
    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $faqCategory->name ?? '') }}" required>
    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="form-group mb-3">
    <label for="description">Description</label>
    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
              rows="3">{{ old('description', $faqCategory->description ?? '') }}</textarea>
    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="row">
    <div class="col-md-6 form-group mb-3">
        <label for="display_order">Display Order</label>
        <input type="number" id="display_order" name="display_order" class="form-control @error('display_order') is-invalid @enderror"
               value="{{ old('display_order', $faqCategory->display_order ?? 0) }}" required min="0" step="1">
         <small class="form-text text-muted">Lower numbers appear first.</small>
        @error('display_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
     <div class="col-md-6 form-group mb-3 align-self-end">
         <div class="form-check form-switch">
             <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                   {{ old('is_active', $faqCategory->is_active ?? true) ? 'checked' : '' }}>
             <label class="form-check-label" for="is_active">Is Active</label>
              @error('is_active')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
         </div>
     </div>
</div>


<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <x-lucide-save class="icon-sm mr-1"/> {{ isset($faqCategory) ? 'Update Category' : 'Create Category' }}
    </button>
    <a href="{{ route('admin.faq-categories.index') }}" class="btn btn-secondary">Cancel</a>
</div>