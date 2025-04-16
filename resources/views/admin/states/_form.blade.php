{{-- resources/views/admin/states/_form.blade.php --}}
@csrf

{{-- Country Selection --}}
<div class="form-group mb-3">
    <label for="country_id">Country <span class="text-danger">*</span></label>
    <select id="country_id" name="country_id" class="form-control select2 @error('country_id') is-invalid @enderror" required>
        <option value="">-- Select Country --</option>
        @foreach($countries as $id => $name)
            <option value="{{ $id }}" {{ old('country_id', $state->country_id ?? '') == $id ? 'selected' : '' }}>
                {{ $name }}
            </option>
        @endforeach
    </select>
    @error('country_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- State Name --}}
<div class="form-group mb-3">
    <label for="name">State/Province/Governorate Name <span class="text-danger">*</span></label>
    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $state->name ?? '') }}" required>
    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- State Code --}}
<div class="form-group mb-3">
    <label for="state_code">State Code (Optional)</label>
    <input type="text" id="state_code" name="state_code" class="form-control @error('state_code') is-invalid @enderror"
           value="{{ old('state_code', $state->state_code ?? '') }}" placeholder="e.g., CA, TX, DU">
    @error('state_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Active Status --}}
<div class="form-group mb-3">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
               {{ old('is_active', $state->is_active ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">Is Active</label>
         @error('is_active')
            <div class="invalid-feedback d-block">{{ $message }}</div>
         @enderror
         <small class="form-text text-muted d-block">Inactive states may not be selectable when adding cities.</small>
    </div>
</div>


<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <x-lucide-save class="icon-sm mr-1"/> {{ isset($state) ? 'Update State' : 'Create State' }}
    </button>
    <a href="{{ route('admin.states.index') }}" class="btn btn-secondary">Cancel</a>
</div>

{{-- Include Select2 JS/CSS if using it for country dropdown --}}
@push('styles')
    {{-- Select2 CSS Link --}}
@endpush
@push('scripts')
    {{-- jQuery & Select2 JS Link --}}
    {{-- <script> $(document).ready(function() { $('.select2').select2({ theme: "bootstrap-5" }); }); </script> --}}
@endpush