@extends('layouts.admin')
@section('title', 'إدارة الطلبات')

@push('styles')
<style>
/* Add styles from customer/product index if needed, plus order-specific ones */
.status-badge { font-size: 0.8em; padding: 3px 8px; border-radius: 5px; font-weight: 500; text-transform: capitalize;}
.status-badge-pending { background-color: #fff3cd; color: #664d03; border: 1px solid #ffeeba;} /* قيد الانتظار */
.status-badge-processing { background-color: #cff4fc; color: #055160; border: 1px solid #bee5eb;} /* قيد المعالجة */
.status-badge-in-check { background-color: #e2e3e5; color: #41464b; border: 1px solid #d3d6d8;} /* قيد المراجعة */
.status-badge-completed { background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc;} /* مكتمل */
.status-badge-cancelled { background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7;} /* ملغي */
.order-id-link { font-weight: 600; text-decoration: none; }
.order-id-link:hover { text-decoration: underline; }
.ws-nowrap { white-space: nowrap; }

/* RTL Adjustments */
html[dir="rtl"] .form-inline .form-group.mr-2 { margin-right: 0 !important; margin-left: 0.5rem !important; }
html[dir="rtl"] .form-inline label.mr-1 { margin-right: 0 !important; margin-left: 0.25rem !important; }
html[dir="rtl"] .form-inline .btn-link.ml-1 { margin-left: 0 !important; margin-right: 0.25rem !important; }
html[dir="rtl"] .mr-1 { margin-right: 0 !important; margin-left: 0.25rem !important; }
html[dir="rtl"] .mr-2 { margin-right: 0 !important; margin-left: 0.5rem !important; }
html[dir="rtl"] .ms-1 { margin-left: 0 !important; margin-right: 0.25rem !important; } /* For icon on right */
html[dir="rtl"] .ms-2 { margin-left: 0 !important; margin-right: 0.5rem !important; } /* For icon on right */
html[dir="rtl"] .text-end { text-align: left !important; } /* For amounts in tables */
html[dir="rtl"] .float-end { float: left !important; } /* For total in footer */
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>إدارة الطلبات</h1>
        <div class="actions">
            <a href="{{ route('admin.orders.create') }}" class="btn btn-primary">
                <x-lucide-plus class="icon-sm ms-2"/> إنشاء طلب يدوي
            </a>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="card mb-4">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="form-inline flex-wrap">
                 {{-- Search --}}
                 <div class="form-group mr-2 mb-2">
                     <label for="search" class="mr-1 d-none d-sm-inline">بحث:</label>
                    <input type="text" id="search" name="search" class="form-control form-control-sm" placeholder="رقم الطلب، رمز التتبع، العميل..." value="{{ request('search') }}">
                </div>
                 {{-- Status --}}
                 <div class="form-group mr-2 mb-2">
                    <label for="filter_status" class="mr-1">الحالة:</label>
                    <select id="filter_status" name="status" class="form-control form-control-sm">
                         <option value="all">كل الحالات</option>
                         @foreach($statuses as $key => $label) {{-- Ensure $statuses has Arabic labels from controller --}}
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                         @endforeach
                    </select>
                </div>
                 {{-- Date Range --}}
                <div class="form-group mr-2 mb-2">
                     <label for="start_date" class="mr-1">من تاريخ:</label>
                     <input type="date" id="start_date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                </div>
                 <div class="form-group mr-2 mb-2">
                     <label for="end_date" class="mr-1">إلى تاريخ:</label>
                     <input type="date" id="end_date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                </div>
                {{-- Submit --}}
                <div class="form-group mb-2">
                    <button type="submit" class="btn btn-secondary btn-sm">
                        <x-lucide-filter class="icon-sm ms-1"/> تصفية
                    </button>
                     @if(request()->hasAny(['search', 'status', 'customer_id', 'start_date', 'end_date']))
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-link btn-sm ml-1">مسح الفلاتر</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>رقم الطلب</th>
                            <th>العميل</th>
                            <th>التاريخ</th>
                            <th>الإجمالي</th>
                            <th>الدفع</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="order-id-link">#{{ $order->id }}</a>
                                    @if($order->track_code)
                                    <small class="d-block text-muted ws-nowrap">تتبع: {{ $order->track_code }}</small>
                                    @endif
                                </td>
                                <td>
                                    {{ $order->customer->name ?? 'غير متوفر' }}
                                    <small class="d-block text-muted">{{ $order->customer->email ?? '' }}</small>
                                </td>
                                <td class="ws-nowrap">{{ $order->created_at->locale('ar')->translatedFormat('d M Y, H:i') }}</td>
                                <td class="ws-nowrap"><strong>دينار {{ number_format($order->total, 2) }}</strong></td> {{-- Changed Currency --}}
                                <td class="ws-nowrap">
                                    @if($order->payment->first()?->status === 'completed')
                                        <span class="text-success">مدفوع</span>
                                    @elseif($order->payment->first()?->status === 'failed')
                                         <span class="text-danger">فشل الدفع</span>
                                    @else
                                         <span class="text-warning">قيد الانتظار</span>
                                    @endif
                                     {{-- Translate payment method codes if possible --}}
                                     <small class="d-block text-muted">{{ Str::ucfirst(str_replace('_', ' ', $order->payment_method_code ?? 'غير محدد')) }}</small>
                                </td>
                                <td class="ws-nowrap">
                                    <span class="status-badge status-badge-{{ str_replace('_','-',$order->status) }}">
                                        {{ $statuses[$order->status] ?? ucfirst($order->status) }} {{-- Ensure $statuses has Arabic labels --}}
                                    </span>
                                </td>
                                <td class="actions ws-nowrap">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-info" title="عرض التفاصيل">
                                        <x-lucide-eye />
                                    </a>
                                    @if(!in_array($order->status, ['completed', 'cancelled']))
                                    <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-sm btn-outline-primary" title="تعديل الطلب">
                                        <x-lucide-pencil />
                                    </a>
                                    @endif
                                    <a href="{{ route('admin.orders.invoice', $order->id) }}" class="btn btn-sm btn-outline-secondary" title="عرض الفاتورة" target="_blank">
                                        <x-lucide-file-text />
                                    </a>
                                    @if(!in_array($order->status, ['completed']))
                                     <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل أنت متأكد من حذف هذا الطلب؟ لا يمكن التراجع عن هذا الإجراء وقد يؤثر على مستويات المخزون.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف الطلب">
                                            <x-lucide-trash-2 />
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">لم يتم العثور على طلبات تطابق بحثك.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($orders->hasPages())
            <div class="card-footer">
                 {{-- <span class="float-end">الإجمالي: دينار {{ number_format($orders->sum('total'), 2) }}</span> --}} {{-- This sums only current page --}}
                 {{ $orders->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection