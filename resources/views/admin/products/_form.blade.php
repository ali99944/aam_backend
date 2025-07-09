{{-- resources/views/admin/products/_form.blade.php --}}

@csrf

<div class="row">
    {{-- Right Column (Main Details) in RTL should appear first if desired visually, or keep LTR structure and let CSS handle it --}}
    {{-- For simplicity of translation, I'll keep the LTR HTML structure and assume `dir="rtl"` on <html> handles visual order --}}

    {{-- Left Column (Main Details) --}}
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">معلومات المنتج</div>
            <div class="card-body">
                 {{-- Name --}}
                <div class="form-group mb-3">
                    <label for="name">اسم المنتج <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $product->name ?? '') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                 {{-- Description --}}
                <div class="form-group mb-3">
                    <label for="description">وصف المنتج <span class="text-danger">*</span></label>
                    {{-- Consider using a Rich Text Editor (like TinyMCE or CKEditor) here --}}
                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                              rows="6" required>{{ old('description', $product->description ?? '') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">صور المنتج</div>
            <div class="card-body">
                 {{-- Main Image --}}
                 <div class="form-group mb-4 border-bottom pb-3">
                    <label for="main_image">الصورة الرئيسية <span class="text-danger">{{ isset($product) ? '' : '*' }}</span></label>
                    <input type="file" id="main_image" name="main_image" class="form-control @error('main_image') is-invalid @enderror" accept="image/*">
                     <small class="form-text text-muted">مطلوبة عند الإنشاء. الصيغ المقبولة: JPG, PNG, GIF, WEBP. الحجم الأقصى: 2 ميجابايت.</small>
                    @error('main_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @if(isset($product) && $product->main_image_url)
                        <div class="mt-2">
                             <p class="mb-1"><small>الصورة الرئيسية الحالية:</small></p>
                            <img src="{{ $product->main_image_url }}" alt="الصورة الرئيسية" style="max-height: 150px;" class="img-thumbnail">
                            {{-- Optional remove checkbox --}}
                        </div>
                    @endif
                </div>

                 {{-- Additional Images --}}
                <div class="form-group mb-3">
                    <label for="additional_images">صور إضافية</label>
                    <input type="file" id="additional_images" name="additional_images[]" class="form-control @error('additional_images.*') is-invalid @enderror" accept="image/*" multiple>
                     <small class="form-text text-muted">يمكنك اختيار عدة صور. الحجم الأقصى لكل صورة: 2 ميجابايت.</small>
                    @error('additional_images.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror {{-- Show array validation errors --}}

                    {{-- Display existing additional images with remove option --}}
                    @if(isset($product) && $product->images->isNotEmpty())
                        <div class="mt-3 existing-images">
                             <p><small>الصور الإضافية الحالية:</small></p>
                            <div class="row">
                            @foreach($product->images as $image)
                                <div class="col-auto mb-2 existing-image-item">
                                     <img src="{{ $image->image_url }}" alt="صورة إضافية" height="80" class="img-thumbnail">
                                     <div class="form-check mt-1">
                                         <input class="form-check-input" type="checkbox" name="remove_images[]" value="{{ $image->id }}" id="remove_image_{{ $image->id }}">
                                         <label class="form-check-label text-danger" for="remove_image_{{ $image->id }}" style="font-size: 0.8em;">
                                             إزالة
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
            <div class="card-header">مواصفات المنتج</div>
            <div class="card-body">
                <div id="specs-container">
                    {{-- Existing Specs --}}
                    @if(isset($product) && $product->specs->isNotEmpty())
                         @foreach($product->specs as $index => $spec)
                         <div class="row spec-item mb-2 align-items-center" data-index="{{ $index }}">
                             <input type="hidden" name="specs[{{ $index }}][id]" value="{{ $spec->id }}">
                             <div class="col-md-5">
                                 <input type="text" name="specs[{{ $index }}][name]" class="form-control form-control-sm @error('specs.'.$index.'.name') is-invalid @enderror" placeholder="اسم المواصفة (مثال: اللون)" value="{{ old('specs.'.$index.'.name', $spec->name) }}">
                                 @error('specs.'.$index.'.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                             </div>
                             <div class="col-md-5">
                                 <input type="text" name="specs[{{ $index }}][value]" class="form-control form-control-sm @error('specs.'.$index.'.value') is-invalid @enderror" placeholder="قيمة المواصفة (مثال: أحمر)" value="{{ old('specs.'.$index.'.value', $spec->value) }}">
                                  @error('specs.'.$index.'.value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                             </div>
                             <div class="col-md-2 text-end"> {{-- text-start for RTL if buttons are on left --}}
                                 <button type="button" class="btn btn-sm btn-outline-danger remove-spec-btn">إزالة</button>
                             </div>
                         </div>
                         @endforeach
                    @elseif(is_array(old('specs')))
                         {{-- Repopulate from old input on validation error --}}
                         @foreach(old('specs') as $index => $specData)
                            <div class="row spec-item mb-2 align-items-center" data-index="{{ $index }}">
                                <input type="hidden" name="specs[{{ $index }}][id]" value="{{ $specData['id'] ?? '' }}">
                                <div class="col-md-5">
                                    <input type="text" name="specs[{{ $index }}][name]" class="form-control form-control-sm @error('specs.'.$index.'.name') is-invalid @enderror" placeholder="اسم المواصفة (مثال: اللون)" value="{{ $specData['name'] ?? '' }}">
                                     @error('specs.'.$index.'.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-5">
                                    <input type="text" name="specs[{{ $index }}][value]" class="form-control form-control-sm @error('specs.'.$index.'.value') is-invalid @enderror" placeholder="قيمة المواصفة (مثال: أحمر)" value="{{ $specData['value'] ?? '' }}">
                                    @error('specs.'.$index.'.value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-2 text-end">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-spec-btn">إزالة</button>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    {{-- Placeholder for new specs --}}
                </div>
                <button type="button" id="add-spec-btn" class="btn btn-sm btn-outline-secondary mt-2">إضافة مواصفة</button>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">إضافات المنتج</div>
            <div class="card-body">
                 <div id="addons-container">
                     {{-- Existing Addons --}}
                    @if(isset($product) && $product->addons->isNotEmpty())
                        @foreach($product->addons as $index => $addon)
                        <div class="row addon-item mb-2 align-items-center" data-index="{{ $index }}">
                            <input type="hidden" name="addons[{{ $index }}][id]" value="{{ $addon->id }}">
                            <div class="col-md-6">
                                <input type="text" name="addons[{{ $index }}][name]" class="form-control form-control-sm @error('addons.'.$index.'.name') is-invalid @enderror" placeholder="اسم الإضافة (مثال: تغليف هدايا)" value="{{ old('addons.'.$index.'.name', $addon->name) }}">
                                @error('addons.'.$index.'.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">دينار</span> {{-- Changed to دينار or your currency --}}
                                    <input type="number" name="addons[{{ $index }}][price]" class="form-control @error('addons.'.$index.'.price') is-invalid @enderror" placeholder="السعر" value="{{ old('addons.'.$index.'.price', $addon->price) }}" step="0.01" min="0">
                                </div>
                                @error('addons.'.$index.'.price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-2 text-end">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-addon-btn">إزالة</button>
                            </div>
                        </div>
                        @endforeach
                    @elseif(is_array(old('addons')))
                        {{-- Repopulate from old input --}}
                         @foreach(old('addons') as $index => $addonData)
                         <div class="row addon-item mb-2 align-items-center" data-index="{{ $index }}">
                             <input type="hidden" name="addons[{{ $index }}][id]" value="{{ $addonData['id'] ?? '' }}">
                             <div class="col-md-6">
                                 <input type="text" name="addons[{{ $index }}][name]" class="form-control form-control-sm @error('addons.'.$index.'.name') is-invalid @enderror" placeholder="اسم الإضافة" value="{{ $addonData['name'] ?? '' }}">
                                 @error('addons.'.$index.'.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                             </div>
                             <div class="col-md-4">
                                 <div class="input-group input-group-sm">
                                     <span class="input-group-text">دينار</span>
                                     <input type="number" name="addons[{{ $index }}][price]" class="form-control @error('addons.'.$index.'.price') is-invalid @enderror" placeholder="السعر" value="{{ $addonData['price'] ?? '' }}" step="0.01" min="0">
                                 </div>
                                 @error('addons.'.$index.'.price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                             </div>
                             <div class="col-md-2 text-end">
                                 <button type="button" class="btn btn-sm btn-outline-danger remove-addon-btn">إزالة</button>
                             </div>
                         </div>
                         @endforeach
                    @endif
                     {{-- Placeholder for new addons --}}
                </div>
                 <button type="button" id="add-addon-btn" class="btn btn-sm btn-outline-secondary mt-2">إضافة ملحق</button>
            </div>
        </div>

    </div>

    {{-- Right Column (Organization, Pricing, Inventory) --}}
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">التصنيف والعلامة التجارية</div>
            <div class="card-body">
                 {{-- Sub Category --}}
                <div class="form-group mb-3">
                    <label for="sub_category_id">الفئة الفرعية <span class="text-danger">*</span></label>
                     <select id="sub_category_id" name="sub_category_id" class="form-control @error('sub_category_id') is-invalid @enderror" required>
                         <option value="">-- اختر فئة فرعية --</option>
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
                    <label for="brand_id">العلامة التجارية <span class="text-danger">*</span></label>
                    <select id="brand_id" name="brand_id" class="form-control @error('brand_id') is-invalid @enderror" required>
                        <option value="">-- اختر علامة تجارية --</option>
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
                    <label for="discount_id">تطبيق خصم</label>
                    <select id="discount_id" name="discount_id" class="form-control @error('discount_id') is-invalid @enderror">
                        <option value="">-- بدون خصم --</option>
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
             <div class="card-header">التسعير والمخزون</div>
             <div class="card-body">
                 {{-- Cost Price --}}
                 <div class="form-group mb-3">
                     <label for="cost_price">سعر التكلفة (دينار) <span class="text-danger">*</span></label>
                     <input type="number" id="cost_price" name="cost_price" class="form-control @error('cost_price') is-invalid @enderror"
                           value="{{ old('cost_price', $product->cost_price ?? '') }}" required step="0.01" min="0">
                     @error('cost_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                 </div>

                 {{-- Sell Price --}}
                 <div class="form-group mb-3">
                     <label for="sell_price">سعر البيع (دينار) <span class="text-danger">*</span></label>
                     <input type="number" id="sell_price" name="sell_price" class="form-control @error('sell_price') is-invalid @enderror"
                            value="{{ old('sell_price', $product->sell_price ?? '') }}" required step="0.01" min="0">
                      @error('sell_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                 </div>

                 <hr> {{-- Separator --}}

                 {{-- Stock --}}
                 <div class="form-group mb-3">
                     <label for="stock">كمية المخزون <span class="text-danger">*</span></label>
                     <input type="number" id="stock" name="stock" class="form-control @error('stock') is-invalid @enderror"
                            value="{{ old('stock', $product->stock ?? 0) }}" required step="1" min="0">
                     @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                 </div>

                  {{-- Low Stock Warning --}}
                 <div class="form-group mb-3">
                     <label for="lower_stock_warn">تنبيه انخفاض المخزون</label>
                     <input type="number" id="lower_stock_warn" name="lower_stock_warn" class="form-control @error('lower_stock_warn') is-invalid @enderror"
                            value="{{ old('lower_stock_warn', $product->lower_stock_warn ?? 0) }}" step="1" min="0">
                     <small class="form-text text-muted">سيتم إشعارك عند وصول المخزون لهذا المستوى (0 لتعطيل التنبيه).</small>
                     @error('lower_stock_warn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                 </div>

                {{-- SKU Code - Now editable --}}
                <div class="form-group mb-3">
                    <label for="sku_code">رمز SKU / الباركود</label>
                    <div class="input-group">
                        <input type="text" id="sku_code" name="sku_code" class="form-control @error('sku_code') is-invalid @enderror"
                            value="{{ old('sku_code', $product->sku_code ?? '') }}" placeholder="أدخل يدوياً أو امسح ضوئياً">
                        <button class="btn btn-outline-secondary" type="button" id="scan_qr_btn_toggle" title="فتح/إغلاق الماسح الضوئي">
                            <x-lucide-scan-barcode />
                        </button>
                    </div>
                    @error('sku_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

             </div>
         </div>

         <!-- Barcode / QR Code Section (Scanner part) -->
         <div class="card mb-4" id="scanner-card" style="display: none;"> {{-- Initially hidden --}}
             <div class="card-header">
                 <h5 class="card-title mb-0">ماسح الباركود / QR</h5>
             </div>
             <div class="card-body">
                 <div id="qr-reader" style="width: 100%;"></div>
                 <div id="qr-reader-results" class="mt-2">
                     <small class="text-muted">سيظهر الرمز الممسوح هنا. انسخه إلى حقل SKU أعلاه.</small>
                 </div>
                  {{-- Display Generated Barcode Image --}}
                  @if(isset($product) && $product->barcode_image_base64) {{-- Assuming you'll add this accessor --}}
                    <div class="mt-3">
                        <label class="form-label">الباركود الحالي للمنتج:</label>
                        <div>
                            <img src="data:image/png;base64,{{ $product->barcode_image_base64 }}" alt="Barcode" style="max-width: 100%; height: auto; border: 1px solid #dee2e6;">
                        </div>
                    </div>
                  @elseif(isset($product) && $product->sku_code)
                    <p class="text-muted mt-2"><small>سيتم إنشاء صورة الباركود بناءً على رمز SKU عند الحفظ.</small></p>
                  @endif
             </div>
         </div>


         <div class="card mb-4">
             <div class="card-header">الحالة والظهور</div>
             <div class="card-body">
                  {{-- Status --}}
                <div class="form-group mb-3">
                    <label for="status">حالة المنتج <span class="text-danger">*</span></label>
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
                        <label class="form-check-label" for="is_public">مرئي للعملاء</label>
                         @error('is_public')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                         @enderror
                    </div>
                     <small class="form-text text-muted">إذا لم يتم تحديده، لن يظهر المنتج في واجهة المتجر.</small>
                </div>
             </div>
         </div>

    </div> {{-- End Right Column --}}
</div> {{-- End Row --}}


<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary btn-lg">
        <x-lucide-save class="icon-sm me-1"/> {{ isset($product) ? 'تحديث المنتج' : 'إنشاء المنتج' }}
    </button>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">إلغاء</a>
</div>


{{-- Templates for Dynamic Specs/Addons (Keep English placeholders for JS, or update JS too) --}}
<template id="spec-template">
     <div class="row spec-item mb-2 align-items-center" data-index="__INDEX__">
         <div class="col-md-5">
             <input type="text" name="specs[__INDEX__][name]" class="form-control form-control-sm" placeholder="اسم المواصفة">
         </div>
         <div class="col-md-5">
             <input type="text" name="specs[__INDEX__][value]" class="form-control form-control-sm" placeholder="قيمة المواصفة">
         </div>
         <div class="col-md-2 text-end">
             <button type="button" class="btn btn-sm btn-outline-danger remove-spec-btn">إزالة</button>
         </div>
     </div>
</template>

<template id="addon-template">
     <div class="row addon-item mb-2 align-items-center" data-index="__INDEX__">
         <div class="col-md-6">
             <input type="text" name="addons[__INDEX__][name]" class="form-control form-control-sm" placeholder="اسم الإضافة">
         </div>
         <div class="col-md-4">
              <div class="input-group input-group-sm">
                 <span class="input-group-text">دينار</span>
                 <input type="number" name="addons[__INDEX__][price]" class="form-control" placeholder="السعر" step="0.01" min="0">
             </div>
         </div>
         <div class="col-md-2 text-end">
             <button type="button" class="btn btn-sm btn-outline-danger remove-addon-btn">إزالة</button>
         </div>
     </div>
</template>

{{-- Add JS for dynamic fields & QR Scanner --}}
@push('scripts')
<script>
// Dynamic Specs/Addons JS (from previous response - keep as is or translate placeholders if you update template above)
document.addEventListener('DOMContentLoaded', function() {
    const specsContainer = document.getElementById('specs-container');
    const specTemplate = document.getElementById('spec-template').innerHTML;
    const addSpecBtn = document.getElementById('add-spec-btn');

    const addonsContainer = document.getElementById('addons-container');
    const addonTemplate = document.getElementById('addon-template').innerHTML;
    const addAddonBtn = document.getElementById('add-addon-btn');

    let specIndex = specsContainer.querySelectorAll('.spec-item').length;
    let addonIndex = addonsContainer.querySelectorAll('.addon-item').length;

    if (addSpecBtn) {
        addSpecBtn.addEventListener('click', function() {
            const newSpecHtml = specTemplate.replace(/__INDEX__/g, specIndex);
            specsContainer.insertAdjacentHTML('beforeend', newSpecHtml);
            specIndex++;
        });
    }

    if (addAddonBtn) {
        addAddonBtn.addEventListener('click', function() {
            const newAddonHtml = addonTemplate.replace(/__INDEX__/g, addonIndex);
            addonsContainer.insertAdjacentHTML('beforeend', newAddonHtml);
            addonIndex++;
        });
    }

    if (specsContainer) {
        specsContainer.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-spec-btn')) {
                event.target.closest('.spec-item').remove();
            }
        });
    }

    if (addonsContainer) {
        addonsContainer.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-addon-btn')) {
                event.target.closest('.addon-item').remove();
            }
        });
    }
});
</script>

{{-- QR/Barcode Scanner JS --}}
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let html5QrcodeScanner;
        const SCANNING_STATE = 2; // Html5Qrcode.STATE_SCANNING (assuming value, might differ)
        const skuCodeInput = document.getElementById('sku_code'); // Target main SKU input
        const qrReaderElement = document.getElementById('qr-reader');
        const scanToggleButton = document.getElementById('scan_qr_btn_toggle');
        const scannerCard = document.getElementById('scanner-card');


        const qrCodeSuccessCallback = (decodedText, decodedResult) => {
            console.log(`تم مسح الرمز: ${decodedText}`, decodedResult);
            if (skuCodeInput) {
                skuCodeInput.value = decodedText; // Populate the main SKU input
            }
            stopScanner();
        };

        const qrCodeErrorCallback = (errorMessage) => {
            // console.warn(`خطأ في مسح رمز QR: ${errorMessage}`);
        };

        function startScanner() {
            if (typeof Html5QrcodeScanner === "undefined") {
                console.error("مكتبة Html5QrcodeScanner غير محملة.");
                alert("مكتبة الماسح الضوئي QR غير محملة. يرجى التحقق من وحدة التحكم.");
                return;
            }
            if (!qrReaderElement) {
                console.error("عنصر قارئ QR ('qr-reader') غير موجود.");
                return;
            }

             // Configuration for the scanner
             const config = {
                fps: 10,
                qrbox: { width: 250, height: 150 }, // Rectangular box might be better for barcodes
                rememberLastUsedCamera: true, // Useful for user experience
                supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA] // Prioritize camera
            };


            scannerCard.style.display = 'block';
            qrReaderElement.style.display = 'block'; // Ensure element is visible
            qrReaderElement.innerHTML = ''; // Clear previous scanner instances

            if (!html5QrcodeScanner || (typeof html5QrcodeScanner.getState === 'function' && html5QrcodeScanner.getState() !== SCANNING_STATE)) {
                try {
                    html5QrcodeScanner = new Html5QrcodeScanner(
                        "qr-reader", // ID of the div to render the scanner
                        config,
                        false // verbose = false
                    );
                    html5QrcodeScanner.render(qrCodeSuccessCallback, qrCodeErrorCallback);
                    scanToggleButton.innerHTML = '<x-lucide-scan-line />'; // Change icon to "Stop Scan"
                    scanToggleButton.setAttribute('title', 'إيقاف الماسح');
                } catch (e) {
                    console.error("خطأ في تهيئة Html5QrcodeScanner:", e);
                    alert("خطأ في تهيئة الماسح الضوئي QR. تحقق من وحدة التحكم.");
                    scannerCard.style.display = 'none';
                }
            }
        }

        function stopScanner() {
            if (html5QrcodeScanner && typeof html5QrcodeScanner.clear === 'function') {
                html5QrcodeScanner.clear().catch(error => {
                    console.error("فشل في مسح html5QrcodeScanner.", error);
                });
                // html5QrcodeScanner = null; // Reset for next scan
            }
            if (qrReaderElement) qrReaderElement.style.display = 'none';
            if (scannerCard) scannerCard.style.display = 'none';
            if (scanToggleButton) {
                scanToggleButton.innerHTML = '<x-lucide-scan-barcode />'; // Change icon back to "Start Scan"
                scanToggleButton.setAttribute('title', 'فتح/إغلاق الماسح الضوئي');
            }
        }


        if (scanToggleButton) {
            scanToggleButton.addEventListener('click', () => {
                // Check if scanner is active by checking display style of the card or qr-reader div
                if (scannerCard.style.display === 'none') {
                    startScanner();
                } else {
                    stopScanner();
                }
            });
        } else {
            console.warn("زر تبديل الماسح ('scan_qr_btn_toggle') غير موجود.");
        }
    });
</script>
@endpush

@push('styles')
<style>
    /* Styles for the input group for SKU */
    .input-group .form-control {
        border-top-right-radius: 0; /* LTR */
        border-bottom-right-radius: 0; /* LTR */
    }
    html[dir="rtl"] .input-group .form-control {
        border-top-right-radius: var(--border-radius, .25rem);
        border-bottom-right-radius: var(--border-radius, .25rem);
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }
    .input-group .btn {
        border-top-left-radius: 0; /* LTR */
        border-bottom-left-radius: 0; /* LTR */
    }
     html[dir="rtl"] .input-group .btn {
        border-top-left-radius: var(--border-radius, .25rem);
        border-bottom-left-radius: var(--border-radius, .25rem);
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    #qr-reader {
        border: 1px solid #dee2e6;
        border-radius: .25rem;
    }
    .img-thumbnail { padding: .25rem; background-color: #fff; border: 1px solid #dee2e6; border-radius: .25rem; max-width: 100%; height: auto; }
    .existing-image-item .form-check { position: absolute; bottom: 5px; right: 10px; background-color: rgba(255,255,255,0.7); padding: 2px 5px; border-radius: 3px;}
    html[dir="rtl"] .existing-image-item .form-check { right: auto; left: 10px; }
    .existing-image-item { position: relative; }
    /* General RTL text alignment (form labels etc. might be handled by global `dir="rtl"` on html/body) */
    .text-end { text-align: left !important; } /* For RTL, text-end should become text-start */
    html[dir="rtl"] .text-end { text-align: right !important; }
    html[dir="rtl"] .input-group-text { border-left: 0; border-right: 1px solid #ced4da; border-radius: 0 var(--border-radius, .25rem) var(--border-radius, .25rem) 0; }
    html[dir="rtl"] .mr-1 { margin-right: 0 !important; margin-left: 0.25rem !important; }
</style>
@endpush