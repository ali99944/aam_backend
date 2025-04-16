{{-- resources/views/admin/products/_form.blade.php --}}

@csrf

<div class="row">
    {{-- Left Column (Main Details) --}}
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">Product Information</div>
            <div class="card-body">
                 {{-- Name --}}
                <div class="form-group mb-3">
                    <label for="name">Product Name <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $product->name ?? '') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                 {{-- Description --}}
                <div class="form-group mb-3">
                    <label for="description">Description <span class="text-danger">*</span></label>
                    {{-- Consider using a Rich Text Editor (like TinyMCE or CKEditor) here --}}
                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                              rows="6" required>{{ old('description', $product->description ?? '') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Images</div>
            <div class="card-body">
                 {{-- Main Image --}}
                 <div class="form-group mb-4 border-bottom pb-3">
                    <label for="main_image">Main Image <span class="text-danger">{{ isset($product) ? '' : '*' }}</span></label>
                    <input type="file" id="main_image" name="main_image" class="form-control @error('main_image') is-invalid @enderror" accept="image/*">
                     <small class="form-text text-muted">Required on create. Accepted formats: JPG, PNG, GIF, WEBP. Max 2MB.</small>
                    @error('main_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @if(isset($product) && $product->main_image_url)
                        <div class="mt-2">
                             <p class="mb-1"><small>Current Main Image:</small></p>
                            <img src="{{ $product->main_image_url }}" alt="Main Image" style="max-height: 150px;" class="img-thumbnail">
                            {{-- Optional remove checkbox --}}
                        </div>
                    @endif
                </div>

                 {{-- Additional Images --}}
                <div class="form-group mb-3">
                    <label for="additional_images">Additional Images</label>
                    <input type="file" id="additional_images" name="additional_images[]" class="form-control @error('additional_images.*') is-invalid @enderror" accept="image/*" multiple>
                     <small class="form-text text-muted">You can select multiple images. Max 2MB each.</small>
                    @error('additional_images.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror {{-- Show array validation errors --}}

                    {{-- Display existing additional images with remove option --}}
                    @if(isset($product) && $product->images->isNotEmpty())
                        <div class="mt-3 existing-images">
                             <p><small>Existing Additional Images:</small></p>
                            <div class="row">
                            @foreach($product->images as $image)
                                <div class="col-auto mb-2 existing-image-item">
                                     <img src="{{ $image->image_url }}" alt="Additional Image" height="80" class="img-thumbnail">
                                     <div class="form-check mt-1">
                                         <input class="form-check-input" type="checkbox" name="remove_images[]" value="{{ $image->id }}" id="remove_image_{{ $image->id }}">
                                         <label class="form-check-label text-danger" for="remove_image_{{ $image->id }}" style="font-size: 0.8em;">
                                             Remove
                                         </label>
                                     </div>
                                </div>
                            @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

         <div class="card mb-4">
            <div class="card-header">Specifications</div>
            <div class="card-body">
                <div id="specs-container">
                    {{-- Existing Specs --}}
                    @if(isset($product) && $product->specs->isNotEmpty())
                         @foreach($product->specs as $index => $spec)
                         <div class="row spec-item mb-2 align-items-center" data-index="{{ $index }}">
                             <input type="hidden" name="specs[{{ $index }}][id]" value="{{ $spec->id }}"> {{-- Keep track of existing ID --}}
                             <div class="col-md-5">
                                 <input type="text" name="specs[{{ $index }}][name]" class="form-control form-control-sm @error('specs.'.$index.'.name') is-invalid @enderror" placeholder="Spec Name (e.g., Color)" value="{{ old('specs.'.$index.'.name', $spec->name) }}">
                                 @error('specs.'.$index.'.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                             </div>
                             <div class="col-md-5">
                                 <input type="text" name="specs[{{ $index }}][value]" class="form-control form-control-sm @error('specs.'.$index.'.value') is-invalid @enderror" placeholder="Spec Value (e.g., Red)" value="{{ old('specs.'.$index.'.value', $spec->value) }}">
                                  @error('specs.'.$index.'.value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                             </div>
                             <div class="col-md-2 text-end">
                                 <button type="button" class="btn btn-sm btn-outline-danger remove-spec-btn">Remove</button>
                             </div>
                         </div>
                         @endforeach
                    @elseif(is_array(old('specs')))
                         {{-- Repopulate from old input on validation error --}}
                         @foreach(old('specs') as $index => $specData)
                            <div class="row spec-item mb-2 align-items-center" data-index="{{ $index }}">
                                <input type="hidden" name="specs[{{ $index }}][id]" value="{{ $specData['id'] ?? '' }}">
                                <div class="col-md-5">
                                    <input type="text" name="specs[{{ $index }}][name]" class="form-control form-control-sm @error('specs.'.$index.'.name') is-invalid @enderror" placeholder="Spec Name (e.g., Color)" value="{{ $specData['name'] ?? '' }}">
                                     @error('specs.'.$index.'.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-5">
                                    <input type="text" name="specs[{{ $index }}][value]" class="form-control form-control-sm @error('specs.'.$index.'.value') is-invalid @enderror" placeholder="Spec Value (e.g., Red)" value="{{ $specData['value'] ?? '' }}">
                                    @error('specs.'.$index.'.value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-2 text-end">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-spec-btn">Remove</button>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    {{-- Placeholder for new specs --}}
                </div>
                <button type="button" id="add-spec-btn" class="btn btn-sm btn-outline-secondary mt-2">Add Specification</button>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Product Addons</div>
            <div class="card-body">
                 <div id="addons-container">
                     {{-- Existing Addons --}}
                    @if(isset($product) && $product->addons->isNotEmpty())
                        @foreach($product->addons as $index => $addon)
                        <div class="row addon-item mb-2 align-items-center" data-index="{{ $index }}">
                            <input type="hidden" name="addons[{{ $index }}][id]" value="{{ $addon->id }}">
                            <div class="col-md-6">
                                <input type="text" name="addons[{{ $index }}][name]" class="form-control form-control-sm @error('addons.'.$index.'.name') is-invalid @enderror" placeholder="Addon Name (e.g., Gift Wrap)" value="{{ old('addons.'.$index.'.name', $addon->name) }}">
                                @error('addons.'.$index.'.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">AED</span>
                                    <input type="number" name="addons[{{ $index }}][price]" class="form-control @error('addons.'.$index.'.price') is-invalid @enderror" placeholder="Price" value="{{ old('addons.'.$index.'.price', $addon->price) }}" step="0.01" min="0">
                                </div>
                                @error('addons.'.$index.'.price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-2 text-end">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-addon-btn">Remove</button>
                            </div>
                        </div>
                        @endforeach
                    @elseif(is_array(old('addons')))
                        {{-- Repopulate from old input --}}
                         @foreach(old('addons') as $index => $addonData)
                         <div class="row addon-item mb-2 align-items-center" data-index="{{ $index }}">
                             <input type="hidden" name="addons[{{ $index }}][id]" value="{{ $addonData['id'] ?? '' }}">
                             <div class="col-md-6">
                                 <input type="text" name="addons[{{ $index }}][name]" class="form-control form-control-sm @error('addons.'.$index.'.name') is-invalid @enderror" placeholder="Addon Name" value="{{ $addonData['name'] ?? '' }}">
                                 @error('addons.'.$index.'.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                             </div>
                             <div class="col-md-4">
                                 <div class="input-group input-group-sm">
                                     <span class="input-group-text">AED</span>
                                     <input type="number" name="addons[{{ $index }}][price]" class="form-control @error('addons.'.$index.'.price') is-invalid @enderror" placeholder="Price" value="{{ $addonData['price'] ?? '' }}" step="0.01" min="0">
                                 </div>
                                 @error('addons.'.$index.'.price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                             </div>
                             <div class="col-md-2 text-end">
                                 <button type="button" class="btn btn-sm btn-outline-danger remove-addon-btn">Remove</button>
                             </div>
                         </div>
                         @endforeach
                    @endif
                     {{-- Placeholder for new addons --}}
                </div>
                 <button type="button" id="add-addon-btn" class="btn btn-sm btn-outline-secondary mt-2">Add Addon</button>
            </div>
        </div>

    </div>

    {{-- Right Column (Organization, Pricing, Inventory) --}}
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">Organization</div>
            <div class="card-body">
                 {{-- Sub Category --}}
                <div class="form-group mb-3">
                    <label for="sub_category_id">Sub Category <span class="text-danger">*</span></label>
                     <select id="sub_category_id" name="sub_category_id" class="form-control @error('sub_category_id') is-invalid @enderror" required>
                         <option value="">-- Select Sub Category --</option>
                         @foreach($subCategories as $categoryName => $subCategoryList)
                            <optgroup label="{{ $categoryName }}">
                                @foreach($subCategoryList as $subCat)
                                    <option value="{{ $subCat->id }}" {{ old('sub_category_id', $product->sub_category_id ?? '') == $subCat->id ? 'selected' : '' }}>
                                        {{ $subCat->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                         @endforeach
                    </select>
                    @error('sub_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                 {{-- Brand --}}
                 <div class="form-group mb-3">
                    <label for="brand_id">Brand <span class="text-danger">*</span></label>
                    <select id="brand_id" name="brand_id" class="form-control @error('brand_id') is-invalid @enderror" required>
                        <option value="">-- Select Brand --</option>
                        @foreach($brands as $id => $name)
                            <option value="{{ $id }}" {{ old('brand_id', $product->brand_id ?? '') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @error('brand_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Discount --}}
                <div class="form-group mb-3">
                    <label for="discount_id">Apply Discount</label>
                    <select id="discount_id" name="discount_id" class="form-control @error('discount_id') is-invalid @enderror">
                        <option value="">-- No Discount --</option>
                         @foreach($discounts as $id => $name)
                            <option value="{{ $id }}" {{ old('discount_id', $product->discount_id ?? '') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @error('discount_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

         <div class="card mb-4">
             <div class="card-header">Pricing & Inventory</div>
             <div class="card-body">
                 {{-- Cost Price --}}
                 <div class="form-group mb-3">
                     <label for="cost_price">Cost Price (AED) <span class="text-danger">*</span></label>
                     <input type="number" id="cost_price" name="cost_price" class="form-control @error('cost_price') is-invalid @enderror"
                           value="{{ old('cost_price', $product->cost_price ?? '') }}" required step="0.01" min="0">
                     @error('cost_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                 </div>

                 {{-- Sell Price --}}
                 <div class="form-group mb-3">
                     <label for="sell_price">Selling Price (AED) <span class="text-danger">*</span></label>
                     <input type="number" id="sell_price" name="sell_price" class="form-control @error('sell_price') is-invalid @enderror"
                            value="{{ old('sell_price', $product->sell_price ?? '') }}" required step="0.01" min="0">
                      @error('sell_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                 </div>

                 <hr> {{-- Separator --}}

                 {{-- Stock --}}
                 <div class="form-group mb-3">
                     <label for="stock">Stock Quantity <span class="text-danger">*</span></label>
                     <input type="number" id="stock" name="stock" class="form-control @error('stock') is-invalid @enderror"
                            value="{{ old('stock', $product->stock ?? 0) }}" required step="1" min="0">
                     @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                 </div>

                  {{-- Low Stock Warning --}}
                 <div class="form-group mb-3">
                     <label for="lower_stock_warn">Low Stock Warning Level</label>
                     <input type="number" id="lower_stock_warn" name="lower_stock_warn" class="form-control @error('lower_stock_warn') is-invalid @enderror"
                            value="{{ old('lower_stock_warn', $product->lower_stock_warn ?? 0) }}" step="1" min="0">
                     <small class="form-text text-muted">Get notified when stock reaches this level (0 to disable).</small>
                     @error('lower_stock_warn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                 </div>

                  {{-- SKU Code --}}
                 <div class="form-group mb-3">
                     <label for="sku_code">SKU Code</label>
                     <input type="text" id="sku_code" name="sku_code" class="form-control @error('sku_code') is-invalid @enderror"
                            value="{{ old('sku_code', $product->sku_code ?? '') }}" placeholder="Leave blank to auto-generate">
                     @error('sku_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                 </div>

             </div>
         </div>

         <div class="card mb-4">
             <div class="card-header">Status & Visibility</div>
             <div class="card-body">
                  {{-- Status --}}
                <div class="form-group mb-3">
                    <label for="status">Product Status <span class="text-danger">*</span></label>
                    <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
                         @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ old('status', $product->status ?? App\Models\Product::STATUS_ACTIVE) == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                         @endforeach
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Visibility --}}
                <div class="form-group mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_public" name="is_public" value="1"
                               {{ old('is_public', $product->is_public ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_public">Visible to Customers</label>
                         @error('is_public')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                         @enderror
                    </div>
                     <small class="form-text text-muted">If unchecked, product will not appear on the storefront.</small>
                </div>
             </div>
         </div>

    </div> {{-- End Right Column --}}
</div> {{-- End Row --}}


<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary btn-lg"> {{-- Larger save button --}}
        <x-lucide-save class="icon-sm mr-1"/> {{ isset($product) ? 'Update Product' : 'Create Product' }}
    </button>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
</div>


{{-- Templates for Dynamic Specs/Addons --}}
<template id="spec-template">
     <div class="row spec-item mb-2 align-items-center" data-index="__INDEX__">
         <div class="col-md-5">
             <input type="text" name="specs[__INDEX__][name]" class="form-control form-control-sm" placeholder="Spec Name (e.g., Color)">
         </div>
         <div class="col-md-5">
             <input type="text" name="specs[__INDEX__][value]" class="form-control form-control-sm" placeholder="Spec Value (e.g., Red)">
         </div>
         <div class="col-md-2 text-end">
             <button type="button" class="btn btn-sm btn-outline-danger remove-spec-btn">Remove</button>
         </div>
     </div>
</template>

<template id="addon-template">
     <div class="row addon-item mb-2 align-items-center" data-index="__INDEX__">
         <div class="col-md-6">
             <input type="text" name="addons[__INDEX__][name]" class="form-control form-control-sm" placeholder="Addon Name (e.g., Gift Wrap)">
         </div>
         <div class="col-md-4">
              <div class="input-group input-group-sm">
                 <span class="input-group-text">AED</span>
                 <input type="number" name="addons[__INDEX__][price]" class="form-control" placeholder="Price" step="0.01" min="0">
             </div>
         </div>
         <div class="col-md-2 text-end">
             <button type="button" class="btn btn-sm btn-outline-danger remove-addon-btn">Remove</button>
         </div>
     </div>
</template>

{{-- Add JS for dynamic fields --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const specsContainer = document.getElementById('specs-container');
    const specTemplate = document.getElementById('spec-template').innerHTML;
    const addSpecBtn = document.getElementById('add-spec-btn');

    const addonsContainer = document.getElementById('addons-container');
    const addonTemplate = document.getElementById('addon-template').innerHTML;
    const addAddonBtn = document.getElementById('add-addon-btn');

    let specIndex = specsContainer.querySelectorAll('.spec-item').length; // Start index based on existing items
    let addonIndex = addonsContainer.querySelectorAll('.addon-item').length;

    // Add Spec
    addSpecBtn.addEventListener('click', function() {
        const newSpecHtml = specTemplate.replace(/__INDEX__/g, specIndex);
        specsContainer.insertAdjacentHTML('beforeend', newSpecHtml);
        specIndex++;
    });

    // Add Addon
    addAddonBtn.addEventListener('click', function() {
        const newAddonHtml = addonTemplate.replace(/__INDEX__/g, addonIndex);
        addonsContainer.insertAdjacentHTML('beforeend', newAddonHtml);
        addonIndex++;
    });

    // Remove Spec (Event Delegation)
    specsContainer.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-spec-btn')) {
            event.target.closest('.spec-item').remove();
            // Note: Re-indexing after removal isn't strictly necessary here
            // as long as the backend handles potentially non-sequential keys,
            // but it can be added if needed for cleaner data submission.
        }
    });

    // Remove Addon (Event Delegation)
    addonsContainer.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-addon-btn')) {
            event.target.closest('.addon-item').remove();
        }
    });
});
</script>
@endpush

{{-- Add styles if needed --}}
@push('styles')
<style>
    .input-group-sm .input-group-text { padding: .25rem .5rem; font-size: .875rem; }
    .img-thumbnail { padding: .25rem; background-color: #fff; border: 1px solid #dee2e6; border-radius: .25rem; max-width: 100%; height: auto; }
    .existing-image-item .form-check { position: absolute; bottom: 5px; right: 10px; background-color: rgba(255,255,255,0.7); padding: 2px 5px; border-radius: 3px;}
    .existing-image-item { position: relative; }
    .text-end { text-align: right !important;}
</style>
@endpush