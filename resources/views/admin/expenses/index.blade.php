{{-- resources/views/admin/expenses/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'إدارة المصروفات')

@push('styles')
<style>
.receipt-link a { text-decoration: none; }
.receipt-link .lucide { vertical-align: middle; }

/* RTL Adjustments if not global */
html[dir="rtl"] .form-inline .form-group.mr-2 { margin-right: 0 !important; margin-left: 0.5rem !important; }
html[dir="rtl"] .form-inline label.mr-1 { margin-right: 0 !important; margin-left: 0.25rem !important; }
html[dir="rtl"] .form-inline .btn-link.ml-1 { margin-left: 0 !important; margin-right: 0.25rem !important; }
html[dir="rtl"] .mr-1 { margin-right: 0 !important; margin-left: 0.25rem !important; }
html[dir="rtl"] .mr-2 { margin-right: 0 !important; margin-left: 0.5rem !important; }
html[dir="rtl"] .ms-1 { margin-left: 0 !important; margin-right: 0.25rem !important; }
html[dir="rtl"] .ms-2 { margin-left: 0 !important; margin-right: 0.5rem !important; }
.ws-nowrap { white-space: nowrap; }
html[dir="rtl"] .float-end { float: left !important; } /* For total in footer */
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>إدارة المصروفات</h1>
        <div class="actions">
            <a href="{{ route('admin.expenses.create') }}" class="btn btn-primary">
                <x-lucide-plus class="icon-sm ms-2"/> تسجيل مصروف جديد
            </a>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="card mb-4">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.expenses.index') }}" class="form-inline flex-wrap">
                 <div class="form-group mr-2 mb-2">
                    <label for="filter_category" class="mr-1">الفئة:</label>
                    <select id="filter_category" name="expense_category_id" class="form-control form-control-sm select2" data-placeholder="كل الفئات"> {{-- Added select2 and placeholder --}}
                         <option value="">كل الفئات</option>
                         @foreach($categories as $id => $name) {{-- Ensure $categories is passed --}}
                            <option value="{{ $id }}" {{ request('expense_category_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                         @endforeach
                    </select>
                </div>
                <div class="form-group mr-2 mb-2">
                     <label for="start_date" class="mr-1">من تاريخ:</label>
                     <input type="date" id="start_date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                </div>
                 <div class="form-group mr-2 mb-2">
                     <label for="end_date" class="mr-1">إلى تاريخ:</label>
                     <input type="date" id="end_date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                </div>
                <div class="form-group mb-2">
                    <button type="submit" class="btn btn-secondary btn-sm">
                        <x-lucide-filter class="icon-sm ms-1"/> تصفية
                    </button>
                     @if(request()->hasAny(['expense_category_id', 'start_date', 'end_date']))
                        <a href="{{ route('admin.expenses.index') }}" class="btn btn-link btn-sm ml-1">مسح الفلاتر</a>
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
                            <th>التاريخ</th>
                            <th>الفئة</th>
                            <th>المبلغ</th>
                            <th>الوصف</th>
                            <th>الإيصال</th>
                            <th>أضيف بواسطة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($expenses as $expense)
                            <tr>
                                <td>{{ $expense->entry_date->locale('ar')->translatedFormat('d M Y') }}</td>
                                <td>{{ $expense->category->name ?? 'غير محدد' }}</td>
                                <td><strong>{{ $expense->formatted_amount }}</strong></td> {{-- Assumes formatted_amount is in JOD/دينار --}}
                                <td>{{ Str::limit($expense->description, 60) }}</td>
                                <td class="text-center receipt-link">
                                    @if($expense->receipt_image_url)
                                        <a href="{{ $expense->receipt_image_url }}" target="_blank" title="عرض الإيصال">
                                            @if (Str::endsWith(strtolower($expense->receipt_image ?? ''), '.pdf')) {{-- Added strtolower for robust check --}}
                                                <x-lucide-file-text class="text-danger"/>
                                            @else
                                                <x-lucide-image class="text-info"/>
                                            @endif
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td><small>{{ $expense->user->name ?? 'النظام' }}</small></td>
                                <td class="actions ws-nowrap">
                                    <a href="{{ route('admin.expenses.edit', $expense->id) }}" class="btn btn-sm btn-outline-primary" title="تعديل">
                                        <x-lucide-pencil />
                                    </a>
                                    <form action="{{ route('admin.expenses.destroy', $expense->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل أنت متأكد من حذف سجل المصروف هذا؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                            <x-lucide-trash-2 />
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">لم يتم العثور على مصروفات تطابق بحثك.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($expenses->hasPages())
            <div class="card-footer">
                @php
                    // Calculate total for the current page of expenses
                    $currentPageTotal = 0;
                    if ($expenses instanceof \Illuminate\Pagination\LengthAwarePaginator) { // Check if it's a paginator instance
                        foreach ($expenses->items() as $exp) {
                            $currentPageTotal += $exp->amount;
                        }
                    }
                @endphp
                 @if($currentPageTotal > 0)
                 <span class="float-end"><strong>إجمالي الصفحة الحالية:</strong> دينار {{ number_format($currentPageTotal, 2) }}</span>
                 @endif
                 {{ $expenses->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    {{-- Select2 JS if using for category filter --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script> --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
    <script>
        // document.addEventListener('DOMContentLoaded', function() {
        //     if (typeof $ !== 'undefined' && typeof $.fn.select2 === 'function') {
        //         $('.select2').each(function() {
        //             $(this).select2({
        //                 theme: "bootstrap-5",
        //                 placeholder: $(this).data('placeholder') || "اختر...",
        //                 dir: "rtl"
        //             });
        //         });
        //     }
        // });
    </script>
@endpush
@push('styles')
    {{-- Select2 CSS --}}
    {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" /> --}}
@endpush