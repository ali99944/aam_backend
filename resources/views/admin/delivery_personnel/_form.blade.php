{{-- resources/views/admin/delivery_personnel/_form.blade.php --}}
@csrf

<div class="row">
    {{-- Left Column --}}
    <div class="col-md-8">
        <div class="card mb-4">
             <div class="card-header">Basic Information</div>
             <div class="card-body">
                {{-- Name --}}
                <div class="form-group mb-3">
                    <label for="name">Full Name <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $deliveryPersonnel->name ?? '') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                 {{-- Contact Row --}}
                 <div class="row">
                     <div class="col-md-6 form-group mb-3">
                        <label for="email">Email Address <span class="text-danger">*</span></label>
                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $deliveryPersonnel->email ?? '') }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label for="phone">Phone Number <span class="text-danger">*</span></label>
                        <input type="tel" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone', $deliveryPersonnel->phone ?? '') }}" required>
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                 </div>
                  {{-- Password Fields --}}
                  <div class="row">
                     <div class="col-md-6 form-group mb-3">
                         <label for="password">Password {{ isset($deliveryPersonnel) ? '(Leave blank to keep current)' : '*' }}</label>
                         <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" {{ isset($deliveryPersonnel) ? '' : 'required' }}>
                         @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                     </div>
                      <div class="col-md-6 form-group mb-3">
                         <label for="password_confirmation">Confirm Password {{ isset($deliveryPersonnel) ? '' : '*' }}</label>
                         <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" {{ isset($deliveryPersonnel) ? '' : 'required' }}>
                     </div>
                 </div>
             </div>
        </div>

         <div class="card mb-4">
             <div class="card-header">Additional Details</div>
             <div class="card-body">
                {{-- Vehicle Row --}}
                 <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label for="vehicle_type">Vehicle Type</label>
                        <input type="text" id="vehicle_type" name="vehicle_type" class="form-control @error('vehicle_type') is-invalid @enderror"
                               value="{{ old('vehicle_type', $deliveryPersonnel->vehicle_type ?? '') }}" placeholder="e.g., Motorcycle, Car">
                        @error('vehicle_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="col-md-6 form-group mb-3">
                        <label for="vehicle_plate_number">Vehicle Plate Number</label>
                        <input type="text" id="vehicle_plate_number" name="vehicle_plate_number" class="form-control @error('vehicle_plate_number') is-invalid @enderror"
                               value="{{ old('vehicle_plate_number', $deliveryPersonnel->vehicle_plate_number ?? '') }}">
                        @error('vehicle_plate_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                 </div>
                  {{-- National ID --}}
                 <div class="form-group mb-3">
                    <label for="national_id_or_iqama">National ID / Iqama</label>
                    <input type="text" id="national_id_or_iqama" name="national_id_or_iqama" class="form-control @error('national_id_or_iqama') is-invalid @enderror"
                           value="{{ old('national_id_or_iqama', $deliveryPersonnel->national_id_or_iqama ?? '') }}">
                    @error('national_id_or_iqama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
             </div>
         </div>
    </div>

    {{-- Right Column --}}
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">Assignment & Status</div>
            <div class="card-body">
                 {{-- Delivery Company --}}
                 <div class="form-group mb-3">
                    <label for="delivery_company_id">Assigned Company</label>
                    <select id="delivery_company_id" name="delivery_company_id" class="form-control @error('delivery_company_id') is-invalid @enderror">
                        <option value="">-- Independent --</option>
                        @foreach($companies as $id => $name)
                            <option value="{{ $id }}" {{ old('delivery_company_id', $deliveryPersonnel->delivery_company_id ?? '') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @error('delivery_company_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                 {{-- Avatar --}}
                <div class="form-group mb-3">
                    <label for="avatar">Avatar/Photo</label>
                    <input type="file" id="avatar" name="avatar" class="form-control @error('avatar') is-invalid @enderror" accept="image/*">
                     <small class="form-text text-muted">Optional. Max 1MB.</small>
                    @error('avatar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @if(isset($deliveryPersonnel) && $deliveryPersonnel->avatar_url)
                        <div class="mt-2 text-center">
                            <img src="{{ $deliveryPersonnel->avatar_url }}" alt="Current Avatar" style="max-height: 120px; border-radius: 50%; border: 1px solid #eee;">
                        </div>
                    @endif
                </div>

                {{-- Active Status --}}
                <div class="form-group mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                               {{ old('is_active', $deliveryPersonnel->is_active ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Is Active</label>
                         @error('is_active')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                         @enderror
                         <small class="form-text text-muted d-block">Inactive personnel cannot be assigned new orders.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <x-lucide-save class="icon-sm mr-1"/> {{ isset($deliveryPersonnel) ? 'Update Delivery Person' : 'Create Delivery Person' }}
    </button>
    <a href="{{ route('admin.delivery-personnel.index') }}" class="btn btn-secondary">Cancel</a>
</div>