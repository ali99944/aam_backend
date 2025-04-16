@csrf
<div class="row">
    <div class="col-md-6 form-group mb-3">
        <label for="name">Currency Name <span class="text-danger">*</span></label>
        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $currency->name ?? '') }}" required placeholder="e.g., UAE Dirham">
         @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-3 form-group mb-3">
        <label for="code">Code <span class="text-danger">*</span></label>
        <input type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $currency->code ?? '') }}" required placeholder="e.g., AED" maxlength="5" style="text-transform:uppercase">
         @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-3 form-group mb-3">
        <label for="symbol">Symbol</label>
        <input type="text" id="symbol" name="symbol" class="form-control @error('symbol') is-invalid @enderror" value="{{ old('symbol', $currency->symbol ?? '') }}" placeholder="e.g., د.إ">
        @error('symbol') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
 <div class="form-group mb-3">
    <label for="exchange_rate">Exchange Rate (vs Base) <span class="text-danger">*</span></label>
    <input type="number" id="exchange_rate" name="exchange_rate" class="form-control @error('exchange_rate') is-invalid @enderror" value="{{ old('exchange_rate', $currency->exchange_rate ?? '1.000000') }}" required step="0.000001" min="0.000001">
    <small>Rate relative to your base currency (e.g., if base is USD, enter AED rate here).</small>
    @error('exchange_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
 <div class="form-group mb-3">
     <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $currency->is_active ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">Is Active</label>
    </div>
</div>
<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary"><x-lucide-save/> {{ isset($currency) ? 'Update' : 'Create' }}</button>
    <a href="{{ route('admin.locations.currencies.index') }}" class="btn btn-secondary">Cancel</a>
</div>