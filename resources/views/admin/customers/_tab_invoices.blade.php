{{-- resources/views/admin/customers/_tab_invoices.blade.php --}}
<div class="table-responsive">
    <table class="table table-sm table-hover admin-table">
        <thead>
            <tr>
                <th>رقم الفاتورة</th>
                <th>رقم الطلب</th>
                <th>تاريخ الإصدار</th>
                <th class="text-end">المبلغ الإجمالي</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->invoice_number }}</td>
                    <td><a href="{{ route('admin.orders.show', $invoice->order_id) }}">#{{ $invoice->order_id }}</a></td>
                    <td>{{ $invoice->issue_date->locale('ar')->translatedFormat('d M Y') }}</td>
                    <td class="text-end">دينار {{ number_format($invoice->total_amount, 2) }}</td> {{-- Changed Currency --}}
                    <td class="actions ws-nowrap">
                        <a href="{{ route('admin.orders.invoice', $invoice->order_id) }}" target="_blank" class="btn btn-xs btn-outline-secondary" title="عرض الفاتورة"><x-lucide-file-text /></a>
                    </td>
                </tr>
            @empty
                 <tr><td colspan="5" class="text-center text-muted py-3">لا توجد فواتير لهذا العميل.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@if ($invoices->hasPages())
<div class="mt-3 d-flex justify-content-center">
    {{ $invoices->appends(['invoices_page' => $invoices->currentPage()])->links() }}
</div>
@endif