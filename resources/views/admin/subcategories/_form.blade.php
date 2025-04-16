{{-- resources/views/admin/subcategories/_form.blade.php --}}

@csrf

<div class="row">
    <div class="col-md-8">
        {{-- Parent Category --}}
        <div class="form-group mb-3">
            <label for="category_id">Parent Category <span class="text-danger">*</span></label>
            <select id="category_id" name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                <option value="">-- Select Parent Category --</option>
                @foreach($categories as $id => $name)
                    <option value="{{ $id }}" {{ old('category_id', $subCategory->category_id ?? '') == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Name --}}
        <div class="form-group mb-3">
            <label for="name">Sub Category Name <span class="text-danger">*</span></label>
            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $subCategory->name ?? '') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Description --}}
        <div class="form-group mb-3">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                      rows="3">{{ old('description', $subCategory->description ?? '') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

         {{-- Cover Image --}}
        <div class="form-group mb-3">
            <label for="cover_image">Cover Image</label>
            <input type="file" id="cover_image" name="cover_image" class="form-control @error('cover_image') is-invalid @enderror" accept="image/jpeg,image/png,image/gif,image/webp">
            <small class="form-text text-muted">Accepted formats: JPG, PNG, GIF, WEBP. Max size: 2MB.</small>
            @error('cover_image')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @if(isset($subCategory) && $subCategory->cover_image_url)
                <div class="mt-2">
                    <img src="{{ $subCategory->cover_image_url }}" alt="Current Cover" style="max-height: 80px; border-radius: 4px;">
                </div>
            @endif
        </div>

        {{-- Icon Image (Optional) --}}
        <div class="form-group mb-3">
            <label for="icon_image">Icon (SVG - Optional)</label>
            <input type="file" id="icon_image" name="icon_image" class="form-control @error('icon_image') is-invalid @enderror" accept="image/svg+xml">
             <small class="form-text text-muted">Accepted format: SVG. Max size: 512KB.</small>
            @error('icon_image')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @if(isset($subCategory) && $subCategory->icon_image_url)
                <div class="mt-2">
                    <img src="{{ $subCategory->icon_image_url }}" alt="Current Icon" style="max-height: 40px; background-color: #f8f9fa; padding: 5px; border-radius: 4px;">
                </div>
            @endif
        </div>

    </div>

    <div class="col-md-4">
         {{-- Active Status --}}
         <div class="form-group mb-3">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                       {{ old('is_active', $subCategory->is_active ?? true) ? 'checked' : '' }}>
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
        <x-lucide-save class="icon-sm mr-1"/> {{ isset($subCategory) ? 'Update Sub Category' : 'Create Sub Category' }}
    </button>
    <a href="{{ route('admin.subcategories.index') }}" class="btn btn-secondary">Cancel</a>
</div>