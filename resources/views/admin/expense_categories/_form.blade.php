{{-- resources/views/admin/expense_categories/_form.blade.php --}}
@csrf

<div class="form-group mb-3">
    <label for="name">Category Name <span class="text-danger">*</span></label>
    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $expenseCategory->name ?? '') }}" required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group mb-3">
    <label for="description">Description</label>
    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
              rows="3">{{ old('description', $expenseCategory->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group mb-3">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
               {{ old('is_active', $expenseCategory->is_active ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">Is Active</label>
         @error('is_active')
            <div class="invalid-feedback d-block">{{ $message }}</div>
         @enderror
         <small class="form-text text-muted d-block">Inactive categories won't be selectable when adding new expenses.</small>
    </div>
</div>


<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <x-lucide-save class="icon-sm mr-1"/> {{ isset($expenseCategory) ? 'Update Category' : 'Create Category' }}
    </button>
    <a href="{{ route('admin.expense-categories.index') }}" class="btn btn-secondary">Cancel</a>
</div>