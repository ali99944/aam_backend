{{-- resources/views/admin/timezones/_form.blade.php --}}
@csrf

{{-- Timezone Name --}}
<div class="form-group mb-3">
    <label for="name">Timezone Identifier <span class="text-danger">*</span></label>
    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $timezone->name ?? '') }}" required placeholder="e.g., Asia/Dubai, America/New_York">
    <small class="form-text text-muted">Use standard Olson database identifiers (e.g., Region/City).</small>
    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Offset String --}}
<div class="form-group mb-3">
    <label for="offset">Offset String</label>
    <input type="text" id="offset" name="offset" class="form-control @error('offset') is-invalid @enderror"
           value="{{ old('offset', $timezone->offset ?? '') }}" placeholder="e.g., UTC+04:00, UTC-05:00">
    <small class="form-text text-muted">Optional. Display offset like UTC+/-HH:MM.</small>
    @error('offset') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- GMT Offset (Seconds) --}}
<div class="form-group mb-3">
    <label for="gmt_offset">GMT Offset (Seconds)</label>
    <input type="number" id="gmt_offset" name="gmt_offset" class="form-control @error('gmt_offset') is-invalid @enderror"
           value="{{ old('gmt_offset', $timezone->gmt_offset ?? '') }}" step="1" placeholder="e.g., 14400, -18000">
    <small class="form-text text-muted">Optional. Offset from GMT in seconds.</small>
    @error('gmt_offset') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Abbreviation --}}
<div class="form-group mb-3">
    <label for="abbreviation">Abbreviation</label>
    <input type="text" id="abbreviation" name="abbreviation" class="form-control @error('abbreviation') is-invalid @enderror"
           value="{{ old('abbreviation', $timezone->abbreviation ?? '') }}" placeholder="e.g., GST, EST, PST">
    <small class="form-text text-muted">Optional. Common abbreviation (can vary with DST).</small>
    @error('abbreviation') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Active Status --}}
<div class="form-group mb-3">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
               {{ old('is_active', $timezone->is_active ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">Is Active</label>
         @error('is_active')
            <div class="invalid-feedback d-block">{{ $message }}</div>
         @enderror
         <small class="form-text text-muted d-block">Inactive timezones may not be selectable.</small>
    </div>
</div>


<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <x-lucide-save class="icon-sm mr-1"/> {{ isset($timezone) ? 'Update Timezone' : 'Create Timezone' }}
    </button>
    <a href="{{ route('admin.timezones.index') }}" class="btn btn-secondary">Cancel</a>
</div>