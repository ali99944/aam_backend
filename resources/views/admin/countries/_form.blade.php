{{-- resources/views/admin/countries/_form.blade.php --}}
@csrf

<div class="row">
    {{-- Left Column --}}
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">Basic Details</div>
            <div class="card-body">
                {{-- Name --}}
                <div class="form-group mb-3">
                    <label for="name">Country Name <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $country->name ?? '') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- ISO Codes Row --}}
                <div class="row">
                     <div class="col-md-4 form-group mb-3">
                        <label for="iso2">ISO Alpha-2 <span class="text-danger">*</span></label>
                        <input type="text" id="iso2" name="iso2" class="form-control @error('iso2') is-invalid @enderror"
                               value="{{ old('iso2', $country->iso2 ?? '') }}" required maxlength="2" style="text-transform: uppercase;">
                        @error('iso2') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="col-md-4 form-group mb-3">
                        <label for="iso3">ISO Alpha-3</label>
                        <input type="text" id="iso3" name="iso3" class="form-control @error('iso3') is-invalid @enderror"
                               value="{{ old('iso3', $country->iso3 ?? '') }}" maxlength="3" style="text-transform: uppercase;">
                         @error('iso3') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="col-md-4 form-group mb-3">
                        <label for="phone_code">Phone Code</label>
                        <input type="text" id="phone_code" name="phone_code" class="form-control @error('phone_code') is-invalid @enderror"
                               value="{{ old('phone_code', $country->phone_code ?? '') }}" placeholder="+971">
                         @error('phone_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                 {{-- Capital --}}
                 <div class="form-group mb-3">
                    <label for="capital">Capital City</label>
                    <input type="text" id="capital" name="capital" class="form-control @error('capital') is-invalid @enderror"
                           value="{{ old('capital', $country->capital ?? '') }}">
                    @error('capital') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="card mb-4">
             <div class="card-header">Regional & Financial</div>
             <div class="card-body">
                 {{-- Region/Subregion Row --}}
                <div class="row">
                     <div class="col-md-6 form-group mb-3">
                        <label for="region">Region</label>
                        <input type="text" id="region" name="region" class="form-control @error('region') is-invalid @enderror"
                               value="{{ old('region', $country->region ?? '') }}" placeholder="e.g., Asia">
                         @error('region') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="col-md-6 form-group mb-3">
                        <label for="subregion">Subregion</label>
                        <input type="text" id="subregion" name="subregion" class="form-control @error('subregion') is-invalid @enderror"
                               value="{{ old('subregion', $country->subregion ?? '') }}" placeholder="e.g., Western Asia">
                         @error('subregion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                 {{-- Currency & Timezone Row --}}
                 <div class="row">
                      <div class="col-md-6 form-group mb-3">
                        <label for="currency_id">Currency</label>
                        <select id="currency_id" name="currency_id" class="form-control select2 @error('currency_id') is-invalid @enderror">
                            <option value="">-- Select Currency --</option>
                            @foreach($currencies as $id => $name)
                                <option value="{{ $id }}" {{ old('currency_id', $country->currency_id ?? '') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                         @error('currency_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="col-md-6 form-group mb-3">
                        <label for="timezone_id">Timezone</label>
                        <select id="timezone_id" name="timezone_id" class="form-control select2 @error('timezone_id') is-invalid @enderror">
                            <option value="">-- Select Timezone --</option>
                             @foreach($timezones as $id => $name)
                                <option value="{{ $id }}" {{ old('timezone_id', $country->timezone_id ?? '') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                         @error('timezone_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                 </div>
            </div>
        </div>
    </div>

    {{-- Right Column --}}
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">Flag & Status</div>
            <div class="card-body">
                 {{-- Flag Image --}}
                <div class="form-group mb-3">
                    <label for="flag_image">Flag Image</label>
                    <input type="file" id="flag_image" name="flag_image" class="form-control @error('flag_image') is-invalid @enderror" accept="image/*">
                     <small class="form-text text-muted">Optional. SVG preferred. Max 512KB.</small>
                    @error('flag_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @if(isset($country) && $country->flag_image_url)
                        <div class="mt-2 text-center">
                            <img src="{{ $country->flag_image_url }}" alt="Current Flag" style="max-height: 60px; border: 1px solid #eee;">
                        </div>
                    @endif
                </div>

                {{-- Active Status --}}
                <div class="form-group mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                               {{ old('is_active', $country->is_active ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Is Active</label>
                         @error('is_active')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                         @enderror
                         <small class="form-text text-muted d-block">Inactive countries may not be selectable elsewhere.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <x-lucide-save class="icon-sm mr-1"/> {{ isset($country) ? 'Update Country' : 'Create Country' }}
    </button>
    <a href="{{ route('admin.countries.index') }}" class="btn btn-secondary">Cancel</a>
</div>

{{-- Include Select2 JS/CSS if using it --}}
@push('styles')
    {{-- Select2 CSS Link --}}
@endpush
@push('scripts')
    {{-- jQuery & Select2 JS Link --}}
    {{-- <script> $(document).ready(function() { $('.select2').select2({ theme: "bootstrap-5" }); }); </script> --}}
@endpush