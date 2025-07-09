@extends('layouts.admin')
@section('title', 'إنشاء طلب جديد')

{{-- Include Select2 or similar library for customer/product search --}}
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
#order-items-table tbody tr td { vertical-align: middle; }
/* Ensure Select2 RTL compatibility */
html[dir="rtl"] .select2-container--bootstrap-5 .select2-selection--single { height: calc(1.5em + .75rem + 2px) !important; padding-right: .75rem; padding-left: 2.25rem }
html[dir="rtl"] .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered { line-height: 1.5 !important; text-align: right; padding-right: 0; padding-left: 1rem;}
html[dir="rtl"] .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow { left: .5rem !important; right: auto !important; top: 50% !important; transform: translateY(-50%) !important; }
html[dir="rtl"] .select2-dropdown { text-align: right; }

.item-total { font-weight: bold; }
.summary-label { font-weight: 500; }
.summary-value { font-weight: bold; }

/* RTL Adjustments for general layout */
html[dir="rtl"] .mr-1 { margin-right: 0 !important; margin-left: 0.25rem !important; }
html[dir="rtl"] .ms-1 { margin-left: 0 !important; margin-right: 0.25rem !important; }
html[dir="rtl"] .text-end { text-align: left !important; } /* For totals */
html[dir="rtl"] .float-end { float: left !important; }
html[dir="rtl"] .input-group-text { border-left: 0; border-right: 1px solid #ced4da;} /* Adjust if using input groups */
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>إنشاء طلب يدوي</h1>
    </div>

    <form method="POST" action="{{ route('admin.orders.store') }}" class="admin-form" id="create-order-form">
        @csrf
        <div class="row">
            {{-- Left Column: Customer & Shipping --}}
            <div class="col-lg-7 col-xl-8">
                <div class="card mb-4">
                    <div class="card-header">معلومات العميل والشحن</div>
                    <div class="card-body">
                        {{-- Customer Selection --}}
                        <div class="form-group mb-3">
                            <label for="customer_id">العميل <span class="text-danger">*</span></label>
                            <select id="customer_id" name="customer_id" class="form-control select2-customer @error('customer_id') is-invalid @enderror" required data-placeholder="اختر عميل...">
                                <option value="">اختر عميل...</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}
                                            data-name="{{ $customer->name }}" data-email="{{ $customer->email }}" data-phone="{{ $customer->phone ?? '' }}">
                                        {{ $customer->name }} ({{ $customer->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <hr>

                        {{-- Shipping Details --}}
                        <h5 class="mb-3">عنوان الشحن</h5>
                         <div class="form-group mb-3">
                            <label for="phone_number">هاتف الاتصال <span class="text-danger">*</span></label>
                            <input type="tel" id="phone_number" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" value="{{ old('phone_number') }}" required>
                            @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                         <div class="form-group mb-3">
                            <label for="address_line_1">العنوان الأول <span class="text-danger">*</span></label>
                            <input type="text" id="address_line_1" name="address_line_1" class="form-control @error('address_line_1') is-invalid @enderror" value="{{ old('address_line_1') }}" required>
                             @error('address_line_1') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                         <div class="form-group mb-3">
                            <label for="address_line_2">العنوان الثاني (اختياري)</label>
                            <input type="text" id="address_line_2" name="address_line_2" class="form-control @error('address_line_2') is-invalid @enderror" value="{{ old('address_line_2') }}">
                             @error('address_line_2') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                         <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label for="city_id">المدينة <span class="text-danger">*</span></label>
                                <select id="city_id" name="city_id" class="form-control select2-basic @error('city_id') is-invalid @enderror" required data-placeholder="اختر مدينة...">
                                     <option value="">اختر مدينة...</option>
                                     @foreach($cities as $id => $name)
                                         <option value="{{ $id }}" {{ old('city_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                     @endforeach
                                </select>
                                @error('city_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                             <div class="col-md-6 form-group mb-3">
                                <label for="postal_code">الرمز البريدي (اختياري)</label>
                                <input type="text" id="postal_code" name="postal_code" class="form-control @error('postal_code') is-invalid @enderror" value="{{ old('postal_code') }}">
                                @error('postal_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                         <div class="form-group mb-3">
                            <label for="special_mark">علامة مميزة (اختياري)</label>
                            <input type="text" id="special_mark" name="special_mark" class="form-control @error('special_mark') is-invalid @enderror" value="{{ old('special_mark') }}">
                             @error('special_mark') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                 <div class="card mb-4">
                     <div class="card-header">ملاحظات الطلب</div>
                     <div class="card-body">
                         <div class="form-group mb-3">
                            <label for="notes">ملاحظات التوصيل (اختياري)</label>
                            <textarea id="notes" name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="form-text text-muted">أي تعليمات خاصة لمندوب التوصيل.</small>
                        </div>
                     </div>
                 </div>
            </div>

            {{-- Right Column: Items & Summary --}}
            <div class="col-lg-5 col-xl-4">
                 <div class="card mb-4">
                    <div class="card-header">منتجات الطلب</div>
                    <div class="card-body">
                        {{-- Product Selection --}}
                        <div class="form-group mb-3">
                            <label for="product_search">إضافة منتج</label>
                            <select id="product_search" class="form-control select2-product" data-placeholder="ابحث عن منتج...">
                                <option value="">ابحث عن منتج...</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->sell_price }}" data-stock="{{ $product->stock }}" data-name="{{ $product->name }}">
                                        {{ $product->name }} (المخزون: {{ $product->stock }}) - دينار {{ number_format($product->sell_price, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Added Items Table --}}
                         <div class="table-responsive mt-3">
                             <table class="table table-sm" id="order-items-table">
                                 <thead>
                                     <tr>
                                         <th>المنتج</th>
                                         <th style="width: 20%;">الكمية</th> {{-- Increased width for Qty input --}}
                                         <th style="width: 30%;">الإجمالي</th> {{-- Increased width for Total --}}
                                         <th style="width: 5%;"></th> {{-- Remove Btn --}}
                                     </tr>
                                 </thead>
                                 <tbody id="order-items-body">
                                     {{-- Items added via JS will appear here --}}
                                 </tbody>
                             </table>
                             @error('items') <div class="text-danger mb-2"><small>{{ $message }}</small></div> @enderror
                         </div>

                         <hr>

                         {{-- Order Summary --}}
                        <div class="order-summary mt-3">
                             <div class="d-flex justify-content-between mb-1">
                                 <span class="summary-label">المجموع الفرعي:</span>
                                 <span class="summary-value" id="summary-subtotal">دينار 0.00</span>
                             </div>
                             <div class="d-flex justify-content-between mb-1">
                                 <span class="summary-label">رسوم التوصيل:</span>
                                 <span class="summary-value" id="summary-delivery">دينار 0.00</span>
                             </div>
                             {{-- <div class="d-flex justify-content-between mb-1">
                                 <span class="summary-label">الخصم:</span>
                                 <span class="summary-value text-danger" id="summary-discount">- دينار 0.00</span>
                             </div> --}}
                             <hr class="my-1">
                             <div class="d-flex justify-content-between mb-1 fs-5">
                                 <span class="summary-label">المجموع الإجمالي:</span>
                                 <span class="summary-value" id="summary-total">دينار 0.00</span>
                             </div>
                         </div>
                    </div>
                </div>

                 <div class="card mb-4">
                    <div class="card-header">حالة الطلب والدفع</div>
                     <div class="card-body">
                         {{-- Status --}}
                        <div class="form-group mb-3">
                            <label for="status">حالة الطلب الأولية <span class="text-danger">*</span></label>
                            <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
                                <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                <option value="processing" {{ old('status') == 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                {{-- Add others if needed --}}
                            </select>
                             @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                         {{-- Payment Method (Optional for manual order) --}}
                         {{-- <div class="form-group mb-3">... Payment method dropdown ...</div> --}}
                     </div>
                </div>

                 {{-- Submit Button --}}
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <x-lucide-save class="icon-sm ms-1"/> إنشاء الطلب
                    </button>
                </div>

            </div> {{-- End Right Column --}}
        </div> {{-- End Row --}}
    </form>
@endsection

@push('scripts')
{{-- jQuery & Select2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // --- Select2 Initialization with Arabic Placeholders ---
        $('.select2-customer').select2({
            theme: "bootstrap-5",
            placeholder: "اختر عميل...",
            dir: "rtl" // Set direction for Select2
        });
        $('.select2-basic').select2({
            theme: "bootstrap-5",
            placeholder: "اختر خيارًا...",
            dir: "rtl"
        });
         $('.select2-product').select2({
            theme: "bootstrap-5",
            placeholder: "ابحث عن منتج...",
            dir: "rtl"
        });

        // --- Auto-fill customer details ---
        $('#customer_id').on('select2:select', function (e) {
            var data = e.params.data;
            var phone = $(data.element).data('phone');
            $('#phone_number').val(phone);
        });

        // --- Delivery Fee Logic ---
        const deliveryFees = @json(\App\Models\DeliveryFee::pluck('amount', 'city_id'));
        const defaultDeliveryFee = {{ config('settings.default_delivery_fee', 7.00) }}; // Example default in JOD

        $('#city_id').on('change', function() {
             updateSummary();
        });

         // --- Add Item Logic ---
        let itemIndex = 0;
        const itemsTableBody = $('#order-items-body');

        $('#product_search').on('select2:select', function (e) {
            var data = e.params.data;
            var productId = data.id;
            if (!productId) return; // Do nothing if placeholder selected

            var productName = $(data.element).data('name');
            var productPrice = parseFloat($(data.element).data('price'));
            var maxStock = parseInt($(data.element).data('stock'));

            if ($(`#order-items-body tr[data-product-id="${productId}"]`).length > 0) {
                 alert('المنتج مضاف بالفعل. يرجى تعديل الكمية.');
                 $('#product_search').val(null).trigger('change');
                 return;
            }
             if (maxStock < 1) {
                 alert('المنتج المختار غير متوفر في المخزون.');
                 $('#product_search').val(null).trigger('change');
                 return;
            }

            const newRow = `
                <tr data-product-id="${productId}" data-price="${productPrice}">
                    <td>
                        <input type="hidden" name="items[${itemIndex}][product_id]" value="${productId}">
                        ${productName}
                    </td>
                    <td>
                        <input type="number" name="items[${itemIndex}][quantity]" value="1" class="form-control form-control-sm item-quantity" min="1" max="${maxStock}" step="1" required style="width: 70px;">
                    </td>
                    <td class="item-total text-end">${formatCurrency(productPrice * 1)}</td>
                    <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-item-btn"><i data-lucide="trash-2" class="icon-xs"></i></button></td>
                </tr>
            `;
            itemsTableBody.append(newRow);
            itemIndex++;
            if (typeof lucide !== 'undefined') { // Check if lucide is available
                lucide.createIcons();
            }
             $('#product_search').val(null).trigger('change');
            updateSummary();
        });

        // --- Remove Item Logic ---
        itemsTableBody.on('click', '.remove-item-btn', function() {
            $(this).closest('tr').remove();
            updateSummary();
        });

         // --- Update Quantity & Line Total ---
        itemsTableBody.on('change input', '.item-quantity', function() {
            let $row = $(this).closest('tr');
            let quantity = parseInt($(this).val()) || 0;
            let price = parseFloat($row.data('price')) || 0;
            let maxStock = parseInt($(this).attr('max')) || 0;

            if (quantity < 1 && $(this).is(':focus')) { // Only reset to 1 if user is interacting
                 // Do not immediately set to 1, allow user to type 0 then correct
            } else if (quantity < 1) {
                quantity = 1;
            }

            if (quantity > maxStock) {
                 quantity = maxStock;
                 alert(`الكمية لا يمكن أن تتجاوز المخزون المتاح (${maxStock}).`);
            }
             $(this).val(quantity);

            let lineTotal = quantity * price;
            $row.find('.item-total').text(formatCurrency(lineTotal));
            updateSummary();
        });
        // Prevent non-numeric input for quantity
        itemsTableBody.on('keypress', '.item-quantity', function(event) {
            if (event.which < 48 || event.which > 57) { // Allow only numbers
                event.preventDefault();
            }
        });


         // --- Update Summary Function ---
        function updateSummary() {
            let subtotal = 0;
            itemsTableBody.find('tr').each(function() {
                let quantity = parseInt($(this).find('.item-quantity').val()) || 0;
                let price = parseFloat($(this).data('price')) || 0;
                subtotal += quantity * price;
            });

            let cityId = $('#city_id').val();
            let deliveryFee = defaultDeliveryFee;
             if (cityId && deliveryFees.hasOwnProperty(cityId)) {
                 deliveryFee = parseFloat(deliveryFees[cityId]);
             }

             let discount = 0.00; // Implement discount logic here
             let total = (subtotal - discount) + deliveryFee;

             $('#summary-subtotal').text(formatCurrency(subtotal));
             $('#summary-delivery').text(formatCurrency(deliveryFee));
             $('#summary-total').text(formatCurrency(total));
        }

         // --- Currency Formatting Helper ---
         function formatCurrency(amount) {
             return 'دينار ' + parseFloat(amount).toFixed(2); // Changed to JOD
         }

        updateSummary(); // Initial call
    });

    // Ensure Lucide icons are rendered if using the JS library
    // document.addEventListener('DOMContentLoaded', function() { if(typeof lucide !== 'undefined') lucide.createIcons(); });
</script>
@endpush