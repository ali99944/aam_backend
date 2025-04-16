{{-- resources/views/admin/policies/_form.blade.php --}}

@csrf

<div class="card">
    <div class="card-body">
         {{-- Name --}}
        <div class="form-group mb-3">
            <label for="name">Policy Name <span class="text-danger">*</span></label>
            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                   {{-- Use $policy->name for edit, empty for create --}}
                   value="{{ old('name', $policy->name ?? '') }}" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

         {{-- Key --}}
        <div class="form-group mb-3">
            <label for="key">Policy Key <span class="text-danger">*</span></label>
            <input type="text" id="key" name="key" class="form-control @error('key') is-invalid @enderror"
                   value="{{ old('key', $policy->key ?? '') }}" required
                   {{-- Make readonly ONLY if the policy already exists (i.e., on edit) --}}
                   {{ $policy->exists ? 'readonly' : '' }}>
            <small class="form-text text-muted">Unique identifier (e.g., 'privacy-policy'). Lowercase, numbers, ., _, -. {{ $policy->exists ? 'Cannot be changed after creation.' : '' }}</small>
            @error('key') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Content (WYSIWYG) --}}
        <div class="form-group mb-3">
            <label for="content">Content <span class="text-danger">*</span></label>
            <textarea id="policy-content-editor" name="content" class="form-control wysiwyg-editor @error('content') is-invalid @enderror"
                      rows="20">{{ old('content', $policy->content ?? '') }}</textarea>
             @error('content') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

         <div class="form-actions mt-4 pt-3 border-top">
            <button type="submit" class="btn btn-primary">
                <x-lucide-save class="icon-sm mr-1"/> {{ $policy->exists ? 'Update Policy' : 'Create Policy' }}
            </button>
            <a href="{{ route('admin.policies.index') }}" class="btn btn-secondary">Cancel</a>
            {{-- Only show Preview button if editing an existing policy --}}
            @if($policy->exists)
            <a href="{{ route('admin.policies.show', $policy->id) }}" class="btn btn-outline-info float-end" target="_blank">
                <x-lucide-eye class="icon-sm mr-1"/> Preview Changes
            </a>
            @endif
         </div>

    </div> {{-- End Card Body --}}
</div> {{-- End Card --}}