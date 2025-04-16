{{-- resources/views/admin/offers/_form.blade.php --}}
@csrf

<div class="row">
    <div class="col-md-8">
        {{-- Title --}}
        <div class="form-group mb-3">
            <label for="title">Offer Title <span class="text-danger">*</span></label>
            <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
                   value="{{ old('title', $offer->title ?? '') }}" required>
            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

         {{-- Slug (Optional) --}}
         {{-- Slug is auto-generated, but allow override --}}
        <div class="form-group mb-3">
            <label for="slug">URL Slug</label>
            <input type="text" id="slug" name="slug" class="form-control @error('slug') is-invalid @enderror"
                   value="{{ old('slug', $offer->slug ?? '') }}" placeholder="Auto-generated from title if left blank">
            <small class="form-text text-muted">Use letters, numbers, dashes, underscores only.</small>
            @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Description --}}
        <div class="form-group mb-3">
            <label for="description">Short Description</label>
            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                      rows="3">{{ old('description', $offer->description ?? '') }}</textarea>
            <small class="form-text text-muted">Optional text shown with the offer banner/link.</small>
            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Offer Image --}}
        <div class="form-group mb-3">
            <label for="image">Offer Image / Banner <span class="text-danger">{{ isset($offer) ? '' : '*' }}</span></label>
            <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
            <small class="form-text text-muted">Required on create. Recommended size: [Your Recommended Size, e.g., 1200x400px]. Max 2MB.</small>
            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
             @if(isset($offer) && $offer->image_url)
                <div class="mt-2">
                     <p class="mb-1"><small>Current Image:</small></p>
                    <img src="{{ $offer->image_url }}" alt="Offer Image" style="max-height: 100px; border-radius: 4px; border: 1px solid #eee;">
                </div>
            @endif
        </div>

    </div>

    <div class="col-md-4">
        {{-- Type --}}
        <div class="form-group mb-3">
            <label for="type">Offer Type / Link <span class="text-danger">*</span></label>
            <select id="type" name="type" class="form-control @error('type') is-invalid @enderror" required onchange="toggleLinkFields()">
                @foreach($types as $key => $label)
                    <option value="{{ $key }}" {{ old('type', $offer->type ?? App\Models\Offer::TYPE_GENERIC) == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Linked ID (Conditional) --}}
        <div id="linked_id_fields" style="display: none;">
            {{-- Category Select --}}
            <div class="form-group mb-3 conditional-link" id="link_category" style="display: none;">
                <label for="linked_id_category">Select Category</label>
                 <select id="linked_id_category" name="linked_id_category" class="form-control select2 @error('linked_id') is-invalid @enderror">
                     <option value="">-- Select --</option>
                      @foreach($categories as $id => $name)
                        <option value="{{ $id }}" {{ (isset($offer) && $offer->type == App\Models\Offer::TYPE_CATEGORY && $offer->linked_id == $id) || old('linked_id_category') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                 </select>
            </div>
            {{-- Product Select --}}
             <div class="form-group mb-3 conditional-link" id="link_product" style="display: none;">
                <label for="linked_id_product">Select Product</label>
                 <select id="linked_id_product" name="linked_id_product" class="form-control select2 @error('linked_id') is-invalid @enderror">
                     <option value="">-- Select --</option>
                     {{-- Note: Limit product list for performance --}}
                      @foreach($products as $id => $name)
                        <option value="{{ $id }}" {{ (isset($offer) && $offer->type == App\Models\Offer::TYPE_PRODUCT && $offer->linked_id == $id) || old('linked_id_product') == $id ? 'selected' : '' }}>{{ Str::limit($name, 50) }}</option>
                    @endforeach
                 </select>
                 <small class="form-text text-muted">Showing limited active products.</small>
            </div>
             {{-- Brand Select --}}
             <div class="form-group mb-3 conditional-link" id="link_brand" style="display: none;">
                <label for="linked_id_brand">Select Brand</label>
                 <select id="linked_id_brand" name="linked_id_brand" class="form-control select2 @error('linked_id') is-invalid @enderror">
                     <option value="">-- Select --</option>
                      @foreach($brands as $id => $name)
                        <option value="{{ $id }}" {{ (isset($offer) && $offer->type == App\Models\Offer::TYPE_BRAND && $offer->linked_id == $id) || old('linked_id_brand') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                 </select>
            </div>
            {{-- Hidden input to consolidate linked_id --}}
            <input type="hidden" name="linked_id" id="linked_id" value="{{ old('linked_id', $offer->linked_id ?? '') }}">
            @error('linked_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        {{-- Target URL (Conditional) --}}
        <div class="form-group mb-3" id="target_url_field" style="display: none;">
            <label for="target_url">Target URL</label>
            <input type="url" id="target_url" name="target_url" class="form-control @error('target_url') is-invalid @enderror"
                   value="{{ old('target_url', $offer->target_url ?? '') }}" placeholder="https://...">
            @error('target_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Date Range --}}
        <div class="row">
            <div class="col-md-6 form-group mb-3">
                <label for="start_date">Start Date</label>
                <input type="datetime-local" id="start_date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                       value="{{ old('start_date', isset($offer->start_date) ? $offer->start_date->format('Y-m-d\TH:i') : '') }}">
                 <small class="form-text text-muted">Optional. Offer active from.</small>
                @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6 form-group mb-3">
                <label for="end_date">End Date</label>
                <input type="datetime-local" id="end_date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
                       value="{{ old('end_date', isset($offer->end_date) ? $offer->end_date->format('Y-m-d\TH:i') : '') }}">
                 <small class="form-text text-muted">Optional. Offer active until.</small>
                @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

         {{-- Sort Order --}}
         <div class="form-group mb-3">
             <label for="sort_order">Sort Order</label>
             <input type="number" id="sort_order" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror"
                    value="{{ old('sort_order', $offer->sort_order ?? 0) }}" step="1">
              <small class="form-text text-muted">Lower numbers display first on the frontend.</small>
             @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
         </div>


         {{-- Active Status --}}
        <div class="form-group mb-3">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                       {{ old('is_active', $offer->is_active ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Is Active</label>
                 @error('is_active')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                 @enderror
                 <small class="form-text text-muted d-block">Controls whether the offer is displayed.</small>
            </div>
        </div>
    </div>
</div>


<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <x-lucide-save class="icon-sm mr-1"/> {{ isset($offer) ? 'Update Offer' : 'Create Offer' }}
    </button>
    <a href="{{ route('admin.offers.index') }}" class="btn btn-secondary">Cancel</a>
</div>

{{-- Include Select2 JS/CSS if using it --}}
@push('styles') @endpush
@push('scripts')
    {{-- jQuery & Select2 JS Link --}}
<script>
    function toggleLinkFields() {
        const offerType = document.getElementById('type').value;
        const linkedIdContainer = document.getElementById('linked_id_fields');
        const targetUrlContainer = document.getElementById('target_url_field');
        const linkCategory = document.getElementById('link_category');
        const linkProduct = document.getElementById('link_product');
        const linkBrand = document.getElementById('link_brand');
        const linkedIdHiddenInput = document.getElementById('linked_id');

        // Select elements inside container
        const selectCategory = document.getElementById('linked_id_category');
        const selectProduct = document.getElementById('linked_id_product');
        const selectBrand = document.getElementById('linked_id_brand');

        // Hide all conditional fields first
        linkedIdContainer.style.display = 'none';
        targetUrlContainer.style.display = 'none';
        linkCategory.style.display = 'none';
        linkProduct.style.display = 'none';
        linkBrand.style.display = 'none';

         // Clear values when type changes to avoid submitting wrong data
        linkedIdHiddenInput.value = '';
         if (selectCategory) selectCategory.value = '';
         if (selectProduct) selectProduct.value = '';
         if (selectBrand) selectBrand.value = '';
         document.getElementById('target_url').value = '';


        // Show relevant fields based on selected type
        if (offerType === '{{ App\Models\Offer::TYPE_GENERIC }}') {
            targetUrlContainer.style.display = 'block';
        } else if (offerType === '{{ App\Models\Offer::TYPE_CATEGORY }}') {
            linkedIdContainer.style.display = 'block';
            linkCategory.style.display = 'block';
             if (selectCategory) linkedIdHiddenInput.value = selectCategory.value; // Initial set
        } else if (offerType === '{{ App\Models\Offer::TYPE_PRODUCT }}') {
            linkedIdContainer.style.display = 'block';
            linkProduct.style.display = 'block';
             if (selectProduct) linkedIdHiddenInput.value = selectProduct.value; // Initial set
        } else if (offerType === '{{ App\Models\Offer::TYPE_BRAND }}') {
            linkedIdContainer.style.display = 'block';
            linkBrand.style.display = 'block';
            if (selectBrand) linkedIdHiddenInput.value = selectBrand.value; // Initial set
        }
    }

     // Event listeners for selects to update the hidden input
     document.getElementById('linked_id_category')?.addEventListener('change', function() {
         document.getElementById('linked_id').value = this.value;
     });
     document.getElementById('linked_id_product')?.addEventListener('change', function() {
         document.getElementById('linked_id').value = this.value;
     });
     document.getElementById('linked_id_brand')?.addEventListener('change', function() {
         document.getElementById('linked_id').value = this.value;
     });

    // Run on page load
    document.addEventListener('DOMContentLoaded', function() {
         toggleLinkFields(); // Initial state
         // If using Select2, initialize it here
         // $('.select2').select2({ theme: "bootstrap-5", width: '100%' });
    });
</script>
@endpush