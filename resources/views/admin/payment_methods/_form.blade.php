{{-- resources/views/admin/payment_methods/_form.blade.php --}}
@csrf

<div class="row">
    {{-- Left Column --}}
    <div class="col-md-8">
        {{-- Basic Info --}}
        <div class="card mb-4">
            <div class="card-header">Basic Information</div>
            <div class="card-body">
                <div class="row">
                     <div class="col-md-6 form-group mb-3">
                        <label for="name">Display Name <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $paymentMethod->name ?? '') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="col-md-6 form-group mb-3">
                        <label for="code">Code <span class="text-danger">*</span></label>
                        <input type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror"
                               value="{{ old('code', $paymentMethod->code ?? '') }}" required pattern="[a-z0-9_]+" title="Lowercase letters, numbers, underscore only">
                        <small class="form-text text-muted">Unique internal identifier (e.g., <code>cod</code>, <code>stripe_card</code>).</small>
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                 <div class="form-group mb-3">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                              rows="2">{{ old('description', $paymentMethod->description ?? '') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                 <div class="form-group mb-3">
                    <label for="instructions">Payment Instructions (Optional)</label>
                    <textarea id="instructions" name="instructions" class="form-control wysiwyg-editor @error('instructions') is-invalid @enderror"
                              rows="4">{{ old('instructions', $paymentMethod->instructions ?? '') }}</textarea>
                     <small class="form-text text-muted">Shown to customer during checkout (e.g., for bank transfer, COD).</small>
                    @error('instructions') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- Gateway & Credentials --}}
        <div class="card mb-4">
             <div class="card-header">Gateway Configuration</div>
             <div class="card-body">
                 <div class="row">
                     <div class="col-md-6 form-group mb-3">
                        <label for="gateway_provider">Gateway Provider</label>
                        {{-- Optional: Make this a select dropdown if you have predefined providers --}}
                        <input type="text" id="gateway_provider" name="gateway_provider" class="form-control @error('gateway_provider') is-invalid @enderror"
                               value="{{ old('gateway_provider', $paymentMethod->gateway_provider ?? '') }}" placeholder="e.g., Stripe, PayPal, PayTabs, COD">
                        <small class="text-muted">Helps identify the integration type.</small>
                        @error('gateway_provider') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="col-md-6 form-group mb-3">
                        <label for="supported_currencies">Supported Currencies</label>
                        <input type="text" id="supported_currencies" name="supported_currencies" class="form-control @error('supported_currencies') is-invalid @enderror"
                               value="{{ old('supported_currencies', $paymentMethod->supported_currencies ?? '') }}" placeholder="e.g., USD, AED, EUR">
                         <small class="text-muted">Comma-separated list (e.g., AED,USD). Leave blank if supports all.</small>
                        @error('supported_currencies') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                 </div>
                  <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label for="api_key">API Key / Public Key</label>
                        <input type="password" id="api_key" name="api_key" class="form-control @error('api_key') is-invalid @enderror" value="{{ old('api_key', $paymentMethod->api_key ?? '') }}">
                        @error('api_key') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="col-md-6 form-group mb-3">
                        <label for="api_secret">API Secret / Secret Key</label>
                        <input type="password" id="api_secret" name="api_secret" class="form-control @error('api_secret') is-invalid @enderror" value="{{ old('api_secret', $paymentMethod->api_secret ?? '') }}">
                        @error('api_secret') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                 <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label for="client_id">Client ID</label>
                        <input type="password" id="client_id" name="client_id" class="form-control @error('client_id') is-invalid @enderror" value="{{ old('client_id', $paymentMethod->client_id ?? '') }}">
                        @error('client_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="col-md-6 form-group mb-3">
                        <label for="client_secret">Client Secret</label>
                        <input type="password" id="client_secret" name="client_secret" class="form-control @error('client_secret') is-invalid @enderror" value="{{ old('client_secret', $paymentMethod->client_secret ?? '') }}">
                        @error('client_secret') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                 <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label for="merchant_id">Merchant ID / Profile ID</label>
                        <input type="password" id="merchant_id" name="merchant_id" class="form-control @error('merchant_id') is-invalid @enderror" value="{{ old('merchant_id', $paymentMethod->merchant_id ?? '') }}">
                        @error('merchant_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="col-md-6 form-group mb-3">
                        <label for="merchant_key">Merchant Key / Server Key</label>
                        <input type="password" id="merchant_key" name="merchant_key" class="form-control @error('merchant_key') is-invalid @enderror" value="{{ old('merchant_key', $paymentMethod->merchant_key ?? '') }}">
                        @error('merchant_key') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                 <div class="form-group mb-3">
                    <label for="redirect_url">Redirect URL / Return URL</label>
                    <input type="url" id="redirect_url" name="redirect_url" class="form-control @error('redirect_url') is-invalid @enderror"
                           value="{{ old('redirect_url', $paymentMethod->redirect_url ?? '') }}" placeholder="https://yourstore.com/payment/callback/gateway">
                    @error('redirect_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                  {{-- Generic Credentials Field (e.g., for JSON config) --}}
                <div class="form-group mb-3">
                    <label for="credentials">Additional Credentials (JSON)</label>
                     <textarea id="credentials" name="credentials" class="form-control @error('credentials') is-invalid @enderror"
                               rows="4" placeholder='Optional JSON for extra settings, e.g., {"webhook_secret": "wh_...", "region": "me-south-1"}'
                               >{{ old('credentials', isset($paymentMethod->credentials) ? json_encode($paymentMethod->credentials, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '') }}</textarea>
                      <small class="text-muted">Store provider-specific settings not covered above as JSON.</small>
                      @error('credentials') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
             </div>
        </div>
    </div>

    {{-- Right Column --}}
    <div class="col-md-4">
         <div class="card mb-4">
            <div class="card-header">Settings</div>
            <div class="card-body">
                {{-- Slug --}}
                <div class="form-group mb-3">
                    <label for="slug">Slug</label>
                    <input type="text" id="slug" name="slug" class="form-control @error('slug') is-invalid @enderror"
                           value="{{ old('slug', $paymentMethod->slug ?? '') }}" pattern="[a-z0-9_-]+" title="Lowercase letters, numbers, underscore, hyphen only">
                    <small class="form-text text-muted">Leave blank to auto-generate from name.</small>
                    @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                 {{-- Image --}}
                <div class="form-group mb-3">
                    <label for="image">Logo/Image <span class="text-danger">{{ isset($paymentMethod) ? '' : '*' }}</span></label>
                    <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                    @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @if(isset($paymentMethod) && $paymentMethod->image_url)
                        <div class="mt-2 text-center">
                            <img src="{{ $paymentMethod->image_url }}" alt="Current Logo" style="max-height: 60px; border: 1px solid #eee; padding: 5px; background: #f8f9fa;">
                        </div>
                    @endif
                </div>
                {{-- Display Order --}}
                 <div class="form-group mb-3">
                    <label for="display_order">Display Order</label>
                    <input type="number" id="display_order" name="display_order" class="form-control @error('display_order') is-invalid @enderror"
                           value="{{ old('display_order', $paymentMethod->display_order ?? 0) }}" required min="0" step="1">
                     <small class="form-text text-muted">Order shown on checkout (0 = first).</small>
                    @error('display_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                 <hr>
                 {{-- Toggles --}}
                 <div class="form-group mb-2">
                     <div class="form-check form-switch">
                         <input class="form-check-input" type="checkbox" id="is_enabled" name="is_enabled" value="1"
                               {{ old('is_enabled', $paymentMethod->is_enabled ?? false) ? 'checked' : '' }}>
                         <label class="form-check-label" for="is_enabled">Enabled</label>
                         <small class="form-text text-muted d-block">Allow customers to select this method.</small>
                     </div>
                 </div>
                 <div class="form-group mb-2">
                     <div class="form-check form-switch">
                         <input class="form-check-input" type="checkbox" id="is_default" name="is_default" value="1"
                               {{ old('is_default', $paymentMethod->is_default ?? false) ? 'checked' : '' }}>
                         <label class="form-check-label" for="is_default">Default</label>
                          <small class="form-text text-muted d-block">Pre-selected on checkout (only one default).</small>
                     </div>
                 </div>
                 <div class="form-group mb-2">
                     <div class="form-check form-switch">
                         <input class="form-check-input" type="checkbox" id="is_test_mode" name="is_test_mode" value="1"
                               {{ old('is_test_mode', $paymentMethod->is_test_mode ?? false) ? 'checked' : '' }}>
                         <label class="form-check-label" for="is_test_mode">Test Mode</label>
                         <small class="form-text text-muted d-block">Use sandbox/test credentials.</small>
                     </div>
                 </div>
                  <div class="form-group mb-2">
                     <div class="form-check form-switch">
                         <input class="form-check-input" type="checkbox" id="is_online" name="is_online" value="1"
                               {{ old('is_online', $paymentMethod->is_online ?? true) ? 'checked' : '' }}>
                         <label class="form-check-label" for="is_online">Online Payment</label>
                         <small class="form-text text-muted d-block">Uncheck for offline methods (e.g., COD, Bank Transfer).</small>
                     </div>
                 </div>
            </div>
         </div>
    </div>
</div>

<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <x-lucide-save class="icon-sm mr-1"/> {{ isset($paymentMethod) ? 'Update Method' : 'Create Method' }}
    </button>
    <a href="{{ route('admin.payment-methods.index') }}" class="btn btn-secondary">Cancel</a>
</div>

{{-- Include scripts/styles for WYSIWYG if used for instructions --}}
{{-- @push('scripts') ... @endpush --}}
{{-- @push('styles') ... @endpush --}}