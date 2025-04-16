{{-- resources/views/admin/suppliers/_form.blade.php --}}
@csrf

<div class="row">
    {{-- Left Column --}}
    <div class="col-md-8">
        {{-- Basic Info --}}
        <div class="card mb-4">
            <div class="card-header">Supplier Information</div>
            <div class="card-body">
                {{-- Name --}}
                <div class="form-group mb-3">
                    <label for="name">Supplier/Company Name <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $supplier->name ?? '') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                {{-- Description --}}
                <div class="form-group mb-3">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                              rows="3">{{ old('description', $supplier->description ?? '') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

         {{-- Contact Info --}}
        <div class="card mb-4">
             <div class="card-header">Contact Details</div>
             <div class="card-body">
                <div class="row">
                     <div class="col-md-6 form-group mb-3">
                        <label for="contact_person">Contact Person</label>
                        <input type="text" id="contact_person" name="contact_person" class="form-control @error('contact_person') is-invalid @enderror"
                               value="{{ old('contact_person', $supplier->contact_person ?? '') }}">
                        @error('contact_person') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="col-md-6 form-group mb-3">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone', $supplier->phone ?? '') }}">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                 <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $supplier->email ?? '') }}">
                         @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label for="website">Website</label>
                        <input type="url" id="website" name="website" class="form-control @error('website') is-invalid @enderror"
                               value="{{ old('website', $supplier->website ?? '') }}" placeholder="https://...">
                        @error('website') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                 {{-- Address --}}
                <div class="form-group mb-3">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror"
                              rows="2">{{ old('address', $supplier->address ?? '') }}</textarea>
                    @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
             </div>
        </div>

        {{-- Internal Notes --}}
        <div class="card mb-4">
            <div class="card-header">Internal Notes</div>
            <div class="card-body">
                 <div class="form-group mb-3">
                    <textarea id="notes" name="notes" class="form-control @error('notes') is-invalid @enderror"
                              rows="3" placeholder="Admin notes about this supplier...">{{ old('notes', $supplier->notes ?? '') }}</textarea>
                    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column --}}
    <div class="col-md-4">
         <div class="card mb-4">
             <div class="card-header">Settings & Status</div>
             <div class="card-body">
                {{-- Image/Logo --}}
                <div class="form-group mb-3">
                    <label for="image">Logo/Image</label>
                    <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                    @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @if(isset($supplier) && $supplier->image_url)
                        <div class="mt-2 text-center">
                            <img src="{{ $supplier->image_url }}" alt="Current Logo" style="max-height: 100px; max-width: 150px; border-radius: 4px; border: 1px solid #eee;">
                        </div>
                    @endif
                </div>

                {{-- Active Status --}}
                <div class="form-group mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                               {{ old('is_active', $supplier->is_active ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Is Active</label>
                         @error('is_active')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                         @enderror
                         <small class="form-text text-muted d-block">Inactive suppliers cannot be used in new purchase orders.</small>
                    </div>
                </div>
             </div>
         </div>

          {{-- Balance Display (Read Only) --}}
         <div class="card mb-4">
             <div class="card-header">Balance</div>
             <div class="card-body">
                 <h4 class="text-center {{ ($supplier->balance ?? 0) == 0 ? 'text-muted' : (($supplier->balance ?? 0) > 0 ? 'text-danger' : 'text-success') }}">
                     {{ $supplier->formatted_balance ?? 'AED 0.00' }}
                 </h4>
                 <small class="d-block text-center text-muted">Balance is updated via Purchase Orders and Payments.</small>
                 {{-- Add links here later to view transactions or record payments --}}
                 {{-- <div class="text-center mt-2">
                     <a href="#" class="btn btn-sm btn-outline-secondary">View Transactions</a>
                 </div> --}}
             </div>
         </div>
    </div>
</div>


<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <x-lucide-save class="icon-sm mr-1"/> {{ isset($supplier) ? 'Update Supplier' : 'Create Supplier' }}
    </button>
    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">Cancel</a>
</div>