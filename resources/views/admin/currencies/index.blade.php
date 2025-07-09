@extends('layouts.admin')
@section('title', 'إدارة العملات')

@push('styles')
{{-- Add specific styles if needed, or ensure they are in common.css/admin.css --}}
<style>
    /* RTL Adjustments for form-inline & icons if not globally handled */
    html[dir="rtl"] .form-inline .form-group.mr-2 { margin-right: 0 !important; margin-left: 0.5rem !important; }
    html[dir="rtl"] .form-inline label.mr-1 { margin-right: 0 !important; margin-left: 0.25rem !important; }
    html[dir="rtl"] .form-inline .btn-link.ml-1 { margin-left: 0 !important; margin-right: 0.25rem !important; }
    html[dir="rtl"] .mr-1 { margin-right: 0 !important; margin-left: 0.25rem !important; }
    html[dir="rtl"] .ms-1 { margin-left: 0 !important; margin-right: 0.25rem !important; } /* For icon on right */
    /* Ensure table actions are on one line in RTL */
    .ws-nowrap { white-space: nowrap; }
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>إدارة العملات</h1>
        {{-- Adjusted icon placement for RTL --}}
        <a href="{{ route('admin.locations.currencies.create') }}" class="btn btn-primary"><x-lucide-plus class="icon-sm ms-1"/> إضافة عملة جديدة</a>
    </div>

     {{-- Filter Form --}}
    <div class="card mb-4">
        <div class="card-body py-2">
             <form method="GET" action="{{ route('admin.locations.currencies.index') }}" class="form-inline flex-wrap"> {{-- Added flex-wrap --}}
                 <div class="form-group mr-2 mb-2">
                     <label for="is_active" class="mr-1">الحالة:</label>
                     <select name="is_active" id="is_active" class="form-control form-control-sm">
                         <option value="all">الكل</option>
                         <option value="1" {{ request('is_active')=='1'?'selected':''}}>فعالة</option>
                         <option value="0" {{ request('is_active')=='0'?'selected':''}}>غير فعالة</option>
                     </select>
                 </div>
                 <div class="form-group mr-2 mb-2">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="الاسم أو الرمز..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-secondary btn-sm"><x-lucide-filter class="icon-sm ms-1"/> تصفية</button>
                @if(request('search') || (request()->filled('is_active') && request('is_active') != 'all'))
                 <a href="{{ route('admin.locations.currencies.index') }}" class="btn btn-link btn-sm ml-1">مسح الفلاتر</a>
                @endif
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>الاسم</th>
                            <th>الرمز</th>
                            <th>الرمز (Symbol)</th>
                            <th>سعر الصرف</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($currencies as $currency)
                            <tr>
                                <td><strong>{{ $currency->name }}</strong></td>
                                <td>{{ $currency->code }}</td>
                                <td>{{ $currency->symbol }}</td>
                                {{-- Ensure proper formatting for high precision numbers if needed --}}
                                <td>{{ rtrim(rtrim(number_format($currency->exchange_rate, 6, '.', ''), '0'), '.') }}</td>
                                <td class="text-center"> {{-- Centered status icon --}}
                                    @if($currency->is_active)
                                        <x-lucide-check-circle class="text-success" title="فعالة"/>
                                    @else
                                        <x-lucide-x-circle class="text-danger" title="غير فعالة"/>
                                    @endif
                                </td>
                                <td class="actions ws-nowrap">
                                    <a href="{{ route('admin.locations.currencies.edit', $currency->id) }}" class="btn btn-sm btn-outline-primary" title="تعديل"><x-lucide-pencil/></a>
                                    <form action="{{ route('admin.locations.currencies.destroy', $currency->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل أنت متأكد من حذف العملة \'{{ $currency->code }}\'؟ قد يؤثر هذا على الدول المرتبطة.');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف"><x-lucide-trash-2/></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-4">لم يتم العثور على عملات.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($currencies->hasPages())
            <div class="card-footer">
                {{ $currencies->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection