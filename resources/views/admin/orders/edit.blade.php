@extends('layouts.admin')
@section('title', "تعديل الطلب رقم #{$order->id}")

@push('styles')
<style>
    /* RTL Adjustments for icons and general spacing */
    html[dir="rtl"] .mr-1 { margin-right: 0 !important; margin-left: 0.25rem !important; }
    html[dir="rtl"] .ms-1 { margin-left: 0 !important; margin-right: 0.25rem !important; }
    html[dir="rtl"] .text-end { text-align: left !important; } /* Align numbers left for amounts in RTL */
    html[dir="rtl"] .float-end { float: left !important; } /* For summary totals */
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>تعديل الطلب رقم #{{ $order->id }}</h1>
        <div class="actions">
             <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-secondary">
                 <x-lucide-eye class="icon-sm ms-1"/> عرض الطلب {{-- Changed to ms-1 for RTL --}}
             </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.orders.update', $order->id) }}" class="admin-form" id="edit-order-form">
        @csrf
        @method('PUT')

        <div class="row">
             {{-- Left Column: Address, Status, Delivery (Optional) --}}
             <div class="col-lg-8">
                  <div class="card mb-4">
                    <div class="card-header">العميل وعنوان الشحن</div>
                     <div class="card-body">
                          {{-- Display Customer Info (Readonly) --}}
                        <div class="mb-3 p-2 bg-light rounded border">
                            <p class="mb-1"><strong>العميل:</strong> {{ $order->customer->name ?? 'غير متوفر' }}</p>
                            <p class="mb-0"><strong>البريد الإلكتروني:</strong> {{ $order->customer->email ?? 'غير متوفر' }}</p>
                        </div>

                        {{-- Editable Shipping Details --}}
                         <div class="form-group mb-3">
                            <label for="phone_number">هاتف الاتصال <span class="text-danger">*</span></label>
                            <input type="tel" id="phone_number" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" value="{{ old('phone_number', $order->phone_number) }}" required>
                            @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                         <div class="form-group mb-3">
                            <label for="address_line_1">العنوان الأول <span class="text-danger">*</span></label>
                            <input type="text" id="address_line_1" name="address_line_1" class="form-control @error('address_line_1') is-invalid @enderror" value="{{ old('address_line_1', $order->address_line_1) }}" required>
                             @error('address_line_1') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                         <div class="form-group mb-3">
                            <label for="address_line_2">العنوان الثاني</label>
                            <input type="text" id="address_line_2" name="address_line_2" class="form-control @error('address_line_2') is-invalid @enderror" value="{{ old('address_line_2', $order->address_line_2) }}">
                             @error('address_line_2') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                         <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label for="city_id">المدينة <span class="text-danger">*</span></label>
                                {{-- Ideally load cities dynamically or ensure current one exists --}}
                                <select id="city_id" name="city_id" class="form-control @error('city_id') is-invalid @enderror" required>
                                     {{-- Ensure $cities is passed to this view or populate dynamically --}}
                                     @if(isset($cities))
                                        @foreach($cities as $id => $name)
                                            <option value="{{ $id }}" {{ old('city_id', $order->city_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                     @else
                                         <option value="{{ $order->city_id }}" selected>{{ $order->city->name ?? 'غير محدد' }}</option>
                                     @endif
                                </select>
                                @error('city_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                             <div class="col-md-6 form-group mb-3">
                                <label for="postal_code">الرمز البريدي</label>
                                <input type="text" id="postal_code" name="postal_code" class="form-control @error('postal_code') is-invalid @enderror" value="{{ old('postal_code', $order->postal_code) }}">
                                @error('postal_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                         <div class="form-group mb-3">
                            <label for="special_mark">علامة مميزة</label>
                            <input type="text" id="special_mark" name="special_mark" class="form-control @error('special_mark') is-invalid @enderror" value="{{ old('special_mark', $order->special_mark) }}">
                             @error('special_mark') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                         <div class="form-group mb-3">
                            <label for="notes">ملاحظات التوصيل</label>
                            <textarea id="notes" name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $order->notes) }}</textarea>
                            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                 {{-- Optional Delivery Assignment Card --}}
                 {{-- <div class="card mb-4">
                     <div class="card-header">تحديد معلومات التوصيل</div>
                     <div class="card-body"> ... حقول لمندوب التوصيل، رقم التتبع ... </div>
                 </div> --}}
             </div>

             {{-- Right Column: Items (Readonly), Status --}}
             <div class="col-lg-4">
                  <div class="card mb-4">
                    <div class="card-header">منتجات الطلب (للعرض فقط)</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead><tr><th>المنتج</th><th>الكمية</th><th class="text-end">الإجمالي</th></tr></thead>
                                <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>{{ $item->product->name ?? 'غير متوفر' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="text-end">دينار {{ number_format($item->total, 2) }}</td> {{-- Changed Currency --}}
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                     <div class="card-footer">
                         {{-- Totals Display --}}
                         <p class="mb-1">المجموع الفرعي: <span class="float-end">دينار {{ number_format($order->subtotal, 2) }}</span></p>
                         @if($order->discount_amount > 0)
                         <p class="mb-1">الخصم: <span class="float-end text-danger">- دينار {{ number_format($order->discount_amount, 2) }}</span></p>
                         @endif
                         <p class="mb-1">رسوم التوصيل: <span class="float-end">دينار {{ number_format($order->delivery_fee, 2) }}</span></p>
                         <hr class="my-1">
                         <p class="mb-0 fs-5 fw-bold">الإجمالي الكلي: <span class="float-end">دينار {{ number_format($order->total, 2) }}</span></p>
                     </div>
                 </div>

                  <div class="card mb-4">
                    <div class="card-header">تحديث حالة الطلب</div>
                     <div class="card-body">
                          <div class="form-group mb-3">
                            <label for="status">حالة الطلب <span class="text-danger">*</span></label>
                            <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
                                 @foreach($statuses as $key => $label) {{-- Make sure $statuses is passed to the view --}}
                                    <option value="{{ $key }}" {{ old('status', $order->status) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                 @endforeach
                            </select>
                             @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                             <small class="form-text text-muted">تغيير الحالة قد يؤثر على مستويات المخزون (مثال: الإلغاء يعيد المخزون).</small>
                        </div>
                     </div>
                  </div>

                 {{-- Submit Button --}}
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <x-lucide-save class="icon-sm ms-1"/> تحديث الطلب
                    </button>
                </div>
             </div>
        </div>
    </form>
@endsection