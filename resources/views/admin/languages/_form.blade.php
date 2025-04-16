{{-- resources/views/admin/languages/_form.blade.php --}}

@csrf

<div class="row">
    <div class="col-md-6">
        {{-- Name --}}
        <div class="form-group mb-3">
            <label for="name">Language Name (e.g., English) <span class="text-danger">*</span></label>
            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $language->name ?? '') }}" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
    <div class="col-md-6">
        {{-- Native Name --}}
        <div class="form-group mb-3">
            <label for="name_native">Native Name (e.g., English / العربية) <span class="text-danger">*</span></label>
            <input type="text" id="name_native" name="name_native" class="form-control @error('name_native') is-invalid @enderror"
                   value="{{ old('name_native', $language->name_native ?? '') }}" required>
            @error('name_native') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
         {{-- Locale Code --}}
        <div class="form-group mb-3">
            <label for="locale">Locale Code (e.g., en, ar, en_US) <span class="text-danger">*</span></label>
            <input type="text" id="locale" name="locale" class="form-control @error('locale') is-invalid @enderror"
                   value="{{ old('locale', $language->locale ?? '') }}" required placeholder="en">
            <small class="form-text text-muted">Must be unique. Used in URLs and code.</small>
            @error('locale') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
     <div class="col-md-6">
        {{-- Direction --}}
        <div class="form-group mb-3">
            <label for="direction">Text Direction <span class="text-danger">*</span></label>
            <select id="direction" name="direction" class="form-control @error('direction') is-invalid @enderror" required>
                 @foreach($directions as $key => $label)
                    <option value="{{ $key }}" {{ old('direction', $language->direction ?? App\Models\Language::DIRECTION_LTR) == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                 @endforeach
            </select>
            @error('direction') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

<div class="row">
     <div class="col-md-6">
         {{-- Flag SVG --}}
        <div class="form-group mb-3">
            <label for="flag_svg">Flag (SVG)</label>
            <input type="file" id="flag_svg" name="flag_svg" class="form-control @error('flag_svg') is-invalid @enderror" accept="image/svg+xml">
             <small class="form-text text-muted">Optional. Max 100KB.</small>
            @error('flag_svg') <div class="invalid-feedback">{{ $message }}</div> @enderror
            @if(isset($language) && $language->flag_svg_url)
                <div class="mt-2">
                    <img src="{{ $language->flag_svg_url }}" alt="{{ $language->name }} Flag" style="height: 25px; border: 1px solid #eee;">
                </div>
            @endif
        </div>
     </div>
     <div class="col-md-6">
         {{-- Active Status --}}
         <div class="form-group mb-3 pt-md-4"> {{-- Adjust padding for alignment --}}
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                       {{ old('is_active', $language->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Is Active</label>
                <small class="form-text text-muted d-block">Inactive languages won't be available for selection.</small>
                 @error('is_active')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                 @enderror
            </div>
        </div>
     </div>
</div>


<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <x-lucide-save class="icon-sm mr-1"/> {{ isset($language) ? 'Update Language' : 'Create Language' }}
    </button>
    <a href="{{ route('admin.languages.index') }}" class="btn btn-secondary">Cancel</a>
</div>