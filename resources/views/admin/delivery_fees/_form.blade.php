{{-- resources/views/admin/delivery_fees/_form.blade.php --}}
@csrf

{{-- City Selection (ONLY ON CREATE) --}}
@if (!isset($deliveryFee))
    <div class="form-group mb-3">
        <label for="city_id">City <span class="text-danger">*</span></label>
        <select id="city_id" name="city_id" class="form-control select2 @error('city_id') is-invalid @enderror" required> {{-- Added select2 class --}}
            <option value="">-- Select City --</option>
            @foreach($availableCities as $id => $name)
                <option value="{{ $id }}" {{ old('city_id') == $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
        @error('city_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @else
            <small class="form-text text-muted">Only cities without an existing fee are shown.</small>
        @enderror
    </div>
@else {{-- Display City Name on Edit --}}
    <div class="form-group mb-3">
        <label>City</label>
        <input type="text" class="form-control" value="{{ $deliveryFee->city->name ?? 'N/A' }}" disabled>
        <small class="form-text text-muted">City cannot be changed. Delete this fee and create a new one if needed.</small>
    </div>
@endif

{{-- Amount --}}
<div class="form-group mb-3">
    <label for="amount">Delivery Fee Amount (AED) <span class="text-danger">*</span></label>
    <input type="number" id="amount" name="amount" class="form-control @error('amount') is-invalid @enderror"
           value="{{ old('amount', $deliveryFee->amount ?? '') }}" required step="0.01" min="0">
    @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Estimated Time --}}
<div class="form-group mb-3">
    <label for="estimated_delivery_time">Estimated Delivery Time</label>
    <input type="text" id="estimated_delivery_time" name="estimated_delivery_time" class="form-control @error('estimated_delivery_time') is-invalid @enderror"
           value="{{ old('estimated_delivery_time', $deliveryFee->estimated_delivery_time ?? '') }}" placeholder="e.g., 1-2 Business Days">
     <small class="form-text text-muted">Optional. Displayed to the customer.</small>
    @error('estimated_delivery_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Notes --}}
<div class="form-group mb-3">
    <label for="notes">Internal Notes</label>
    <textarea id="notes" name="notes" class="form-control @error('notes') is-invalid @enderror"
              rows="2">{{ old('notes', $deliveryFee->notes ?? '') }}</textarea>
    <small class="form-text text-muted">Optional notes for admin reference.</small>
    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Active Status --}}
<div class="form-group mb-3">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
               {{ old('is_active', $deliveryFee->is_active ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">Is Active</label>
         @error('is_active')
            <div class="invalid-feedback d-block">{{ $message }}</div>
         @enderror
         <small class="form-text text-muted d-block">If inactive, this city-specific fee will not apply.</small>
    </div>
</div>


<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <x-lucide-save class="icon-sm mr-1"/> {{ isset($deliveryFee) ? 'Update Fee' : 'Create Fee' }}
    </button>
    <a href="{{ route('admin.delivery-fees.index') }}" class="btn btn-secondary">Cancel</a>
</div>

{{-- Include Select2 JS/CSS if using it for the city dropdown --}}
@push('styles')
    {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
    {{-- <link rel="stylesheet" href="{{ asset('path/to/select2.min.css') }}"> --}}
    {{-- Add Select2 theme if needed --}}
@endpush
@push('scripts')
    {{-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script> --}} {{-- Select2 usually needs jQuery --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
    {{-- <script src="{{ asset('path/to/select2.min.js') }}"></script> --}}
    <script>
        // document.addEventListener('DOMContentLoaded', function() {
        //     $('.select2').select2({ theme: "bootstrap-5" }); // Initialize Select2 if included
        // });
    </script>
@endpush