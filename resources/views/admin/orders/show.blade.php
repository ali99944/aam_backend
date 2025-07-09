@extends('layouts.admin')
@section('title', "تفاصيل الطلب رقم #{$order->id}")

@push('styles')
<style>
/* Styles from index */
.status-badge { font-size: 0.8em; padding: 3px 8px; border-radius: 5px; font-weight: 500; text-transform: capitalize;}
.status-badge-pending { background-color: #fff3cd; color: #664d03; border: 1px solid #ffeeba;} /* قيد الانتظار */
.status-badge-processing { background-color: #cff4fc; color: #055160; border: 1px solid #bee5eb;} /* قيد المعالجة */
.status-badge-in-check { background-color: #e2e3e5; color: #41464b; border: 1px solid #d3d6d8;} /* قيد المراجعة */
.status-badge-completed { background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc;} /* مكتمل */
.status-badge-cancelled { background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7;} /* ملغي */
.item-image { height: 50px; width: 50px; object-fit: cover; border-radius: 3px; border: 1px solid #eee; } /* Added border */
.order-summary-table td { border: none !important; padding: 0.3rem 0 !important;}
.order-summary-table tr:last-child td { font-weight: bold; padding-top: 0.5rem !important; border-top: 1px solid #eee !important;}

/* RTL Adjustments for icons and general spacing */
html[dir="rtl"] .mr-1 { margin-right: 0 !important; margin-left: 0.25rem !important; }
html[dir="rtl"] .ms-1 { margin-left: 0 !important; margin-right: 0.25rem !important; }
html[dir="rtl"] .me-1 { margin-left: 0.25rem !important; margin-right: 0 !important; } /* For icons before text in RTL */
html[dir="rtl"] .text-end { text-align: left !important; } /* Align numbers to the left for amounts in RTL */
html[dir="rtl"] .card-footer .ms-auto { margin-left: auto !important; margin-right: 0 !important; }
</style>
@endpush


@section('content')
    <div class="content-header">
        <h1>تفاصيل الطلب رقم #{{ $order->id }}</h1>
        <div class="actions">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                 <x-lucide-arrow-right class="icon-sm ms-1"/> العودة إلى الطلبات
             </a>
            @if(!in_array($order->status, ['completed', 'cancelled']))
                <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-primary">
                    <x-lucide-pencil class="icon-sm ms-1"/> تعديل الطلب
                </a>
            @endif
            <a href="{{ route('admin.orders.invoice', $order->id) }}" class="btn btn-outline-secondary" target="_blank">
                <x-lucide-file-text class="icon-sm ms-1"/> عرض الفاتورة
            </a>
        </div>
    </div>

    <div class="row">
        {{-- Left Column: Order Details, Items, Delivery --}}
        <div class="col-lg-8">
            {{-- Order Status & Summary Card --}}
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                     <span>ملخص الطلب</span>
                     <span class="status-badge status-badge-{{ str_replace('_','-',$order->status) }}">
                         {{-- Translate status value if you have a helper or array --}}
                         {{ \App\Models\Order::statuses()[$order->status] ?? ucfirst($order->status) }}
                     </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                             <p class="mb-1"><strong>تاريخ الطلب:</strong> {{ $order->created_at->locale('ar')->translatedFormat('d M Y, H:i A') }}</p>
                             <p class="mb-1"><strong>رمز التتبع:</strong> {{ $order->track_code ?? 'غير متوفر' }}</p>
                        </div>
                         <div class="col-md-6">
                             <p class="mb-1"><strong>طريقة الدفع:</strong> {{ Str::ucfirst(str_replace('_', ' ', $order->payment_method_code ?? 'غير محدد')) }}</p>
                            <p class="mb-1"><strong>حالة الدفع:</strong>
                                @if($order->payment->first()?->status === 'completed') <span class="text-success">مدفوع</span>
                                @elseif($order->payment->first()?->status === 'failed') <span class="text-danger">فشل الدفع</span>
                                @else <span class="text-warning">قيد الانتظار</span> @endif
                                @if($order->payment->first()?->transaction_id) (معرف: {{ $order->payment->first()->transaction_id }}) @endif
                             </p>
                         </div>
                    </div>
                    @if($order->notes)
                        <hr><p class="mb-1"><strong>ملاحظات العميل:</strong> {{ $order->notes }}</p>
                    @endif
                </div>
            </div>

            {{-- Order Items Card --}}
            <div class="card mb-4">
                <div class="card-header">منتجات الطلب</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                         <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 10%">صورة</th>
                                    <th>المنتج</th>
                                    <th class="text-center">الكمية</th>
                                    <th class="text-end">سعر الوحدة</th>
                                    <th class="text-end">الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td>
                                            <img src="{{ $item->product?->main_image_url ?? asset('images/placeholder-product.png') }}" alt="{{ $item->product?->name }}" class="item-image">
                                        </td>
                                        <td>
                                            {{ $item->product?->name ?? 'المنتج غير موجود' }}
                                             @if($item->product?->sku_code)<small class="d-block text-muted">SKU: {{ $item->product->sku_code }}</small>@endif
                                        </td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">دينار {{ number_format($item->price, 2) }}</td>
                                        <td class="text-end">دينار {{ number_format($item->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                         </table>
                    </div>
                </div>
                <div class="card-footer">
                     <table class="ms-auto order-summary-table" style="width: 300px; max-width: 100%;">
                         <tr><td>المجموع الفرعي:</td><td class="text-end">دينار {{ number_format($order->subtotal, 2) }}</td></tr>
                         @if($order->discount_amount > 0)
                         <tr><td>الخصم:</td><td class="text-end text-danger">- دينار {{ number_format($order->discount_amount, 2) }}</td></tr>
                         @endif
                         <tr><td>رسوم التوصيل:</td><td class="text-end">دينار {{ number_format($order->delivery_fee, 2) }}</td></tr>
                         <tr class="fs-5"><td>المجموع الإجمالي:</td><td class="text-end">دينار {{ number_format($order->total, 2) }}</td></tr>
                    </table>
                </div>
            </div>

            {{-- Delivery Info Card --}}
            <div class="card mb-4">
                 <div class="card-header">معلومات التوصيل</div>
                 <div class="card-body">
                     @if($order->delivery)
                         @php $delivery = $order->delivery; @endphp
                          <p class="mb-1"><strong>الحالة:</strong> <span class="text-capitalize">{{ $delivery->status ?? 'غير محدد' }}</span></p>
                          <p class="mb-1"><strong>شركة التوصيل:</strong> {{ $delivery->deliveryCompany->name ?? 'غير محدد' }}</p>
                          <p class="mb-1"><strong>مندوب التوصيل:</strong> {{ $delivery->deliveryPersonnel->name ?? 'غير محدد' }}</p>
                          <p class="mb-1"><strong>رقم التتبع:</strong> {{ $delivery->tracking_number ?? 'غير متوفر' }}</p>
                          <p class="mb-1"><strong>تاريخ التوصيل المتوقع/الفعلي:</strong> {{ $delivery->delivery_date ?? 'لم يحدد بعد' }}</p>
                          @if($delivery->confirmation_image)
                          <p class="mb-0"><strong>تأكيد الاستلام:</strong> <a href="{{ Storage::disk('public')->url($delivery->confirmation_image) }}" target="_blank">عرض الصورة</a></p>
                          @endif
                     @else
                         <p class="text-muted">لم يتم تحديد معلومات التوصيل بعد.</p>
                     @endif
                 </div>
            </div>
        </div>

        {{-- Right Column: Customer & Address --}}
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">تفاصيل العميل</div>
                <div class="card-body">
                     @if($order->customer)
                         <p class="mb-1"><strong>الاسم:</strong> {{ $order->customer->name }}</p>
                         <p class="mb-1"><strong>البريد الإلكتروني:</strong> <a href="mailto:{{ $order->customer->email }}">{{ $order->customer->email }}</a></p>
                         <p class="mb-0"><strong>الهاتف:</strong> {{ $order->phone_number }}</p>
                         {{-- <a href="{{ route('admin.customers.show', $order->customer_id) }}" class="btn btn-sm btn-outline-secondary mt-2">عرض ملف العميل</a> --}}
                     @else
                         <p class="text-danger">العميل غير موجود.</p>
                     @endif
                 </div>
            </div>

             <div class="card mb-4">
                <div class="card-header">عنوان الشحن</div>
                <div class="card-body">
                     <p class="mb-1">{{ $order->address_line_1 }}</p>
                     @if($order->address_line_2) <p class="mb-1">{{ $order->address_line_2 }}</p> @endif
                     <p class="mb-1">{{ $order->city->name ?? 'غير محدد' }}</p>
                     @if($order->postal_code) <p class="mb-1">الرمز البريدي: {{ $order->postal_code }}</p> @endif
                     @if($order->special_mark) <p class="mb-0">علامة مميزة: {{ $order->special_mark }}</p> @endif
                 </div>
            </div>

             <div class="card mb-4">
                 <div class="card-header">الفاتورة</div>
                 <div class="card-body">
                    @if($order->invoice)
                        <p class="mb-1"><strong>رقم الفاتورة:</strong> {{ $order->invoice->invoice_number }}</p>
                        <p class="mb-1"><strong>تاريخ الإصدار:</strong> {{ $order->invoice->issue_date->locale('ar')->translatedFormat('d M Y') }}</p>
                        <a href="{{ route('admin.orders.invoice', $order->id) }}" class="btn btn-sm btn-outline-secondary mt-2" target="_blank">
                            <x-lucide-file-text class="icon-xs ms-1"/> عرض الفاتورة كاملة
                        </a>
                    @else
                        <p class="text-muted">لم يتم إنشاء الفاتورة بعد.</p>
                    @endif
                 </div>
             </div>
        </div>
    </div>
@endsection