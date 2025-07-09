{{-- resources/views/admin/customers/_tab_orders.blade.php --}}
<div class="table-responsive">
    <table class="table table-sm table-hover admin-table">
        <thead>
            <tr>
                <th>رقم الطلب</th>
                <th>التاريخ</th>
                <th>الحالة</th>
                <th>المدينة</th>
                <th class="text-end">الإجمالي</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td><a href="{{ route('admin.orders.show', $order->id) }}">#{{ $order->id }}</a></td>
                    <td>{{ $order->created_at->locale('ar')->translatedFormat('d M Y') }}</td>
                    <td>
                        <span class="status-badge status-badge-{{ str_replace('_','-',$order->status) }}">
                            {{-- Translate status if you have a helper/array, otherwise ucfirst --}}
                            {{ \App\Models\Order::statuses()[$order->status] ?? ucfirst($order->status) }}
                        </span>
                    </td>
                    <td>{{ $order->city->name ?? 'غير محدد' }}</td>
                    <td class="text-end">دينار {{ number_format($order->total, 2) }}</td> {{-- Changed Currency --}}
                    <td class="actions ws-nowrap">
                         <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-xs btn-outline-info" title="عرض الطلب"><x-lucide-eye /></a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-3">لا توجد طلبات لهذا العميل.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@if ($orders->hasPages())
<div class="mt-3 d-flex justify-content-center">
    {{ $orders->appends(['orders_page' => $orders->currentPage()])->links() }}
</div>
@endif