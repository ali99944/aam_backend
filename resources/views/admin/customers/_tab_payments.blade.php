{{-- resources/views/admin/customers/_tab_payments.blade.php --}}
<div class="table-responsive">
    <table class="table table-sm table-hover admin-table">
        <thead>
            <tr>
                <th>معرف الدفع</th>
                <th>رقم الطلب</th>
                <th>التاريخ</th>
                <th>طريقة الدفع</th>
                <th>الحالة</th>
                <th class="text-end">المبلغ</th>
                <th>معرف المعاملة</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payments as $payment)
                <tr>
                    <td>#{{ $payment->id }}</td>
                    <td><a href="{{ route('admin.orders.show', $payment->order_id) }}">#{{ $payment->order_id }}</a></td>
                    <td>{{ $payment->created_at->locale('ar')->translatedFormat('d M Y H:i') }}</td>
                    <td>{{ $payment->paymentMethod->name ?? 'غير محدد' }}</td>
                    <td>
                       {{-- Translate status values if possible --}}
                       <span class="text-capitalize status-badge status-badge-{{ $payment->status }}">{{ $payment->status }}</span>
                    </td>
                    <td class="text-end">دينار {{ number_format($payment->amount, 2) }}</td> {{-- Changed Currency --}}
                    <td>{{ $payment->transaction_id ?? '-' }}</td>
                </tr>
            @empty
                 <tr><td colspan="7" class="text-center text-muted py-3">لا توجد مدفوعات لهذا العميل.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@if ($payments->hasPages())
<div class="mt-3 d-flex justify-content-center">
   {{ $payments->appends(['payments_page' => $payments->currentPage()])->links() }}
</div>
@endif