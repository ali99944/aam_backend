{{-- resources/views/admin/discounts/_form.blade.php --}}

@csrf

<div class="row">
    <div class="col-md-8">
        {{-- Name --}}
        <div class="form-group mb-3">
            <label for="name">Discount Name <span class="text-danger">*</span></label>
            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $discount->name ?? '') }}" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

         {{-- Code --}}
        <div class="form-group mb-3">
            <label for="code">Discount Code</label>
            <input type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror"
                   value="{{ old('code', $discount->code ?? '') }}" placeholder="Leave blank to auto-generate">
             <small class="form-text text-muted">Optional. Used for coupons. Only letters, numbers, underscores, hyphens.</small>
            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="row">
            {{-- Type --}}
            <div class="col-md-6 form-group mb-3">
                <label for="type">Discount Type <span class="text-danger">*</span></label>
                <select id="type" name="type" class="form-control @error('type') is-invalid @enderror" required>
                    @foreach($discountTypes as $key => $label)
                        <option value="{{ $key }}" {{ old('type', $discount->type ?? App\Models\Discount::TYPE_PERCENTAGE) == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Value --}}
            <div class="col-md-6 form-group mb-3">
                <label for="value">Value <span class="text-danger">*</span></label>
                <input type="number" id="value" name="value" class="form-control @error('value') is-invalid @enderror"
                       value="{{ old('value', $discount->value ?? '') }}" required step="0.01" min="0">
                 <small class="form-text text-muted">Enter amount (e.g., 10.50) or percentage (e.g., 15).</small>
                @error('value') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

         {{-- Description --}}
        <div class="form-group mb-3">
            <label for="description">Internal Description</label>
            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                      rows="3">{{ old('description', $discount->description ?? '') }}</textarea>
            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

    </div>

    <div class="col-md-4">
        {{-- Status --}}
        <div class="form-group mb-3">
            <label for="status">Status <span class="text-danger">*</span></label>
            <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
                @foreach($statuses as $key => $label)
                    <option value="{{ $key }}" {{ old('status', $discount->status ?? App\Models\Discount::STATUS_INACTIVE) == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Expiration Type --}}
        <div class="form-group mb-3">
             <label for="expiration_type">Expiration Type <span class="text-danger">*</span></label>
             <select id="expiration_type" name="expiration_type" class="form-control @error('expiration_type') is-invalid @enderror" required onchange="toggleExpirationFields()">
                 @foreach($expirationTypes as $key => $label)
                    <option value="{{ $key }}" {{ old('expiration_type', $discount->expiration_type ?? App\Models\Discount::EXPIRATION_NONE) == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                 @endforeach
            </select>
             @error('expiration_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Duration Field (Conditional) --}}
        <div class="form-group mb-3" id="duration_fields" style="display: none;">
             <label for="duration_days">Duration (Days)</label>
             <input type="number" id="duration_days" name="duration_days" class="form-control @error('duration_days') is-invalid @enderror"
                    value="{{ old('duration_days', $discount->duration_days ?? '') }}" min="1" step="1">
             <small class="form-text text-muted">Number of days the discount is valid after activation.</small>
             @error('duration_days') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Period Fields (Conditional) --}}
        <div id="period_fields" style="display: none;">
             <div class="form-group mb-3">
                 <label for="start_date">Start Date</label>
                 <input type="datetime-local" id="start_date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                        value="{{ old('start_date', isset($discount->start_date) ? $discount->start_date->format('Y-m-d\TH:i') : '') }}">
                 @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
             </div>
             <div class="form-group mb-3">
                 <label for="end_date">End Date</label>
                 <input type="datetime-local" id="end_date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
                        value="{{ old('end_date', isset($discount->end_date) ? $discount->end_date->format('Y-m-d\TH:i') : '') }}">
                @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
             </div>
        </div>

    </div> {{-- End Col MD 4 --}}
</div> {{-- End Row --}}


<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <x-lucide-save class="icon-sm mr-1"/> {{ isset($discount) ? 'Update Discount' : 'Create Discount' }}
    </button>
    <a href="{{ route('admin.discounts.index') }}" class="btn btn-secondary">Cancel</a>
</div>

{{-- Script to show/hide conditional expiration fields --}}
@push('scripts')
<script>
    function toggleExpirationFields() {
        const expirationType = document.getElementById('expiration_type').value;
        const durationFields = document.getElementById('duration_fields');
        const periodFields = document.getElementById('period_fields');
        const durationInput = document.getElementById('duration_days');
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        // Hide all conditional fields initially
        durationFields.style.display = 'none';
        periodFields.style.display = 'none';
        // Clear inputs when hiding to avoid validation issues? Optional.
        // durationInput.value = '';
        // startDateInput.value = '';
        // endDateInput.value = '';

        // Show relevant fields based on selection
        if (expirationType === '{{ App\Models\Discount::EXPIRATION_DURATION }}') {
            durationFields.style.display = 'block';
        } else if (expirationType === '{{ App\Models\Discount::EXPIRATION_PERIOD }}') {
            periodFields.style.display = 'block';
        }
    }

    // Run on page load in case of validation errors or editing
    document.addEventListener('DOMContentLoaded', toggleExpirationFields);
</script>
@endpush