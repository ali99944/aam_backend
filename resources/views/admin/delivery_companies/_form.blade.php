{{-- resources/views/admin/delivery_companies/_form.blade.php --}}
@csrf

<div class="row">
    <div class="col-md-8">
        {{-- Name --}}
        <div class="form-group mb-3">
            <label for="name">Company Name <span class="text-danger">*</span></label>
            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $deliveryCompany->name ?? '') }}" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

         {{-- Description --}}
        <div class="form-group mb-3">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                      rows="3">{{ old('description', $deliveryCompany->description ?? '') }}</textarea>
            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Contact Info Row --}}
        <div class="row">
             <div class="col-md-6 form-group mb-3">
                <label for="contact_phone">Contact Phone</label>
                <input type="tel" id="contact_phone" name="contact_phone" class="form-control @error('contact_phone') is-invalid @enderror"
                       value="{{ old('contact_phone', $deliveryCompany->contact_phone ?? '') }}">
                @error('contact_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
             <div class="col-md-6 form-group mb-3">
                <label for="contact_email">Contact Email</label>
                <input type="email" id="contact_email" name="contact_email" class="form-control @error('contact_email') is-invalid @enderror"
                       value="{{ old('contact_email', $deliveryCompany->contact_email ?? '') }}">
                 @error('contact_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

         {{-- Address --}}
        <div class="form-group mb-3">
            <label for="address">Address</label>
            <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror"
                      rows="2">{{ old('address', $deliveryCompany->address ?? '') }}</textarea>
            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

         {{-- Tracking URL --}}
        <div class="form-group mb-3">
            <label for="tracking_url_pattern">Tracking URL Pattern</label>
            <input type="url" id="tracking_url_pattern" name="tracking_url_pattern" class="form-control @error('tracking_url_pattern') is-invalid @enderror"
                   value="{{ old('tracking_url_pattern', $deliveryCompany->tracking_url_pattern ?? '') }}" placeholder="e.g., https://track.site/?id={tracking_number}">
             <small class="form-text text-muted">Optional. Use '{tracking_number}' as a placeholder for the actual tracking ID.</small>
            @error('tracking_url_pattern') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

    </div>

    <div class="col-md-4">
        {{-- Logo --}}
        <div class="form-group mb-3">
            <label for="logo">Company Logo</label>
            <input type="file" id="logo" name="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
             <small class="form-text text-muted">Optional. Max 1MB.</small>
            @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
            @if(isset($deliveryCompany) && $deliveryCompany->logo_url)
                <div class="mt-2 text-center">
                    <img src="{{ $deliveryCompany->logo_url }}" alt="Current Logo" style="max-height: 100px; max-width: 150px; border-radius: 4px; border: 1px solid #eee;">
                    {{-- Optional: Add remove checkbox --}}
                </div>
            @endif
        </div>

         {{-- Active Status --}}
        <div class="form-group mb-3">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                       {{ old('is_active', $deliveryCompany->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Is Active</label>
                 @error('is_active')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                 @enderror
                 <small class="form-text text-muted d-block">Inactive companies cannot be assigned to new orders.</small>
            </div>
        </div>
    </div>
</div>


<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <x-lucide-save class="icon-sm mr-1"/> {{ isset($deliveryCompany) ? 'Update Company' : 'Create Company' }}
    </button>
    <a href="{{ route('admin.delivery-companies.index') }}" class="btn btn-secondary">Cancel</a>
</div>