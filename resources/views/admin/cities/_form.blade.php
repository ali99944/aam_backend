{{-- resources/views/admin/cities/_form.blade.php --}}
@csrf

{{-- State Selection --}}
<div class="form-group mb-3">
    <label for="state_id">State/Province/Governorate <span class="text-danger">*</span></label>
    <select id="state_id" name="state_id" class="form-control select2 @error('state_id') is-invalid @enderror" required>
        <option value="">-- Select State/Province --</option>
        {{-- Option 1: Grouped Dropdown --}}
        @foreach($statesGrouped as $countryName => $stateList)
            <optgroup label="{{ $countryName }}">
                @foreach($stateList as $state)
                    <option value="{{ $state->id }}" {{ old('state_id', $city->state_id ?? '') == $state->id ? 'selected' : '' }}>
                        {{ $state->name }}
                    </option>
                @endforeach
            </optgroup>
        @endforeach
        {{-- Option 2: Flat List (if not using grouped) --}}
        {{-- @foreach($states as $id => $name)
            <option value="{{ $id }}" {{ old('state_id', $city->state_id ?? '') == $id ? 'selected' : '' }}>
                {{ $name }}
            </option>
        @endforeach --}}
    </select>
    @error('state_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    {{-- Note: Consider JS to dynamically load states based on a country filter if the list is very long --}}
</div>

{{-- City Name --}}
<div class="form-group mb-3">
    <label for="name">City Name <span class="text-danger">*</span></label>
    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $city->name ?? '') }}" required>
    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Coordinates Row --}}
<div class="row">
     <div class="col-md-6 form-group mb-3">
        <label for="latitude">Latitude</label>
        <input type="number" id="latitude" name="latitude" class="form-control @error('latitude') is-invalid @enderror"
               value="{{ old('latitude', $city->latitude ?? '') }}" step="0.0000001" min="-90" max="90" placeholder="e.g., 25.2048">
         @error('latitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
     <div class="col-md-6 form-group mb-3">
        <label for="longitude">Longitude</label>
        <input type="number" id="longitude" name="longitude" class="form-control @error('longitude') is-invalid @enderror"
               value="{{ old('longitude', $city->longitude ?? '') }}" step="0.0000001" min="-180" max="180" placeholder="e.g., 55.2708">
        @error('longitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
     <small class="form-text text-muted px-3 mb-3">Optional. Used for mapping or distance calculations.</small>
</div>


{{-- Active Status --}}
<div class="form-group mb-3">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
               {{ old('is_active', $city->is_active ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">Is Active</label>
         @error('is_active')
            <div class="invalid-feedback d-block">{{ $message }}</div>
         @enderror
         <small class="form-text text-muted d-block">Inactive cities may not be available for selection in addresses or delivery fees.</small>
    </div>
</div>


<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <x-lucide-save class="icon-sm mr-1"/> {{ isset($city) ? 'Update City' : 'Create City' }}
    </button>
    <a href="{{ route('admin.cities.index') }}" class="btn btn-secondary">Cancel</a>
</div>

{{-- Include Select2 JS/CSS if using it --}}
@push('styles') @endpush
@push('scripts')
    {{-- <script> $(document).ready(function() { $('.select2').select2({ theme: "bootstrap-5" }); }); </script> --}}
@endpush