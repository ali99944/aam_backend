{{-- resources/views/admin/categories/_form.blade.php --}}

@csrf {{-- CSRF Token included here --}}

<div class="row"> {{-- Use row/col for better layout optional --}}
    <div class="col-md-8">
        <div class="form-group mb-3">
            <label for="name">Category Name</label>
            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $category->name ?? '') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                      rows="4">{{ old('description', $category->description ?? '') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="cover_image">Cover Image</label>
            <input type="file" id="cover_image" name="cover_image" class="form-control @error('cover_image') is-invalid @enderror" accept="image/jpeg,image/png,image/gif,image/webp">
            <small class="form-text text-muted">Accepted formats: JPG, PNG, GIF, WEBP. Max size: 2MB.</small>
            @error('cover_image')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            {{-- Display current cover image --}}
            @if(isset($category) && $category->cover_image_url)
                <div class="mt-2">
                    <img src="{{ $category->cover_image_url }}" alt="Current Cover Image" style="max-height: 100px; border-radius: 4px;">
                </div>
            @endif
        </div>

        <div class="form-group mb-3">
            <label for="icon_image">Icon (SVG)</label>
            <input type="file" id="icon_image" name="icon_image" class="form-control @error('icon_image') is-invalid @enderror" accept="image/svg+xml">
             <small class="form-text text-muted">Accepted format: SVG. Max size: 512KB.</small>
            @error('icon_image')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
             {{-- Display current icon --}}
            @if(isset($category) && $category->icon_image_url)
                <div class="mt-2">
                    <img src="{{ $category->icon_image_url }}" alt="Current Icon" style="max-height: 50px; background-color: #f8f9fa; padding: 5px; border-radius: 4px;">
                </div>
            @endif
        </div>

    </div>

    <div class="col-md-4">
         <div class="form-group mb-3">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                       {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Is Active</label>
                 @error('is_active')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                 @enderror
            </div>
        </div>
    </div>

</div>


<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <x-lucide-save class="icon-sm mr-1"/> {{ isset($category) ? 'Update Category' : 'Create Category' }}
    </button>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancel</a>
</div>