{{-- resources/views/admin/brands/_form.blade.php --}}

@csrf

<div class="row">
    <div class="col-md-8"> {{-- Main content column --}}
        {{-- Brand Name --}}
        <div class="form-group mb-3">
            <label for="name">Brand Name <span class="text-danger">*</span></label>
            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $brand->name ?? '') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Brand Image --}}
        <div class="form-group mb-3">
            <label for="image">Brand Logo/Image</label>
            <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/gif,image/webp">
            <small class="form-text text-muted">Optional. Accepted formats: JPG, PNG, GIF, WEBP. Max size: 1MB.</small>
            @error('image')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            {{-- Display current image --}}
            @if(isset($brand) && $brand->image_url)
                <div class="mt-2">
                    <p class="mb-1"><small>Current Image:</small></p>
                    <img src="{{ $brand->image_url }}" alt="Current Brand Image" style="max-height: 80px; border-radius: 4px; border: 1px solid #eee; padding: 3px;">
                    {{-- Optional: Add a checkbox to remove the image --}}
                    {{--
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image" value="1">
                        <label class="form-check-label" for="remove_image">
                            Remove current image
                        </label>
                    </div>
                    --}}
                </div>
            @endif
        </div>
    </div>

    <div class="col-md-4"> {{-- Sidebar column (if needed for future fields like status) --}}
        {{-- Add other fields here if necessary, e.g., status, featured flag --}}
        <p class="text-muted"><small>Additional settings can be placed here.</small></p>
    </div>
</div>


<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <x-lucide-save class="icon-sm mr-1"/> {{ isset($brand) ? 'Update Brand' : 'Create Brand' }}
    </button>
    <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">Cancel</a>
</div>