@extends('layouts.admin')
@section('title', 'إدارة الدول')

@push('styles')
<style>
.table-flag-preview { height: 20px; width: auto; max-width: 35px; vertical-align: middle; border: 1px solid #eee; }
.status-badge { font-size: 0.8em; padding: 3px 8px; border-radius: 5px; font-weight: 500; } /* Added font-weight */
.status-badge-active { background-color: #d1e7dd; color: #0f5132; }
.status-badge-inactive { background-color: #e2e3e5; color: #41464b; }

/* RTL Adjustments for form-inline & icons if not globally handled */
html[dir="rtl"] .form-inline .form-group.mr-2 { margin-right: 0 !important; margin-left: 0.5rem !important; }
html[dir="rtl"] .form-inline label.mr-1 { margin-right: 0 !important; margin-left: 0.25rem !important; }
html[dir="rtl"] .form-inline .btn-link.ml-2 { margin-left: 0 !important; margin-right: 0.5rem !important; }
html[dir="rtl"] .mr-1 { margin-right: 0 !important; margin-left: 0.25rem !important; }
html[dir="rtl"] .mr-2 { margin-right: 0 !important; margin-left: 0.5rem !important; }
html[dir="rtl"] .ms-1 { margin-left: 0 !important; margin-right: 0.25rem !important; }
html[dir="rtl"] .ms-2 { margin-left: 0 !important; margin-right: 0.5rem !important; }
.ws-nowrap { white-space: nowrap; }
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>إدارة الدول</h1>
        <div class="actions">
            <a href="{{ route('admin.countries.create') }}" class="btn btn-primary">
                <x-lucide-plus class="icon-sm ms-2"/> إضافة دولة جديدة
            </a>
        </div>
    </div>

    {{-- Filter/Search Form --}}
    <div class="card mb-4">
        <div class="card-body py-2">
             <form method="GET" action="{{ route('admin.countries.index') }}" class="form-inline flex-wrap"> {{-- Added flex-wrap --}}
                 <div class="form-group mr-2 mb-2">
                     <label for="search" class="mr-1 d-none d-sm-inline">بحث:</label>
                    <input type="text" id="search" name="search" class="form-control form-control-sm" placeholder="الاسم، الرمز، العاصمة..." value="{{ request('search') }}">
                </div>
                {{-- <div class="form-group mr-2 mb-2">
                    <label for="filter_currency" class="mr-1">العملة:</label>
                    <select id="filter_currency" name="currency_id" class="form-control form-control-sm select2">
                        <option value="">كل العملات</option>
                        @foreach($currencies as $id => $name)
                            <option value="{{ $id }}" {{ request('currency_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div> --}}
                 <div class="form-group mr-2 mb-2">
                    <label for="filter_active" class="mr-1">الحالة:</label>
                    <select id="filter_active" name="is_active" class="form-control form-control-sm">
                         <option value="all">الكل</option>
                         <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>فعالة</option>
                         <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>غير فعالة</option>
                    </select>
                </div>
                <div class="form-group mb-2">
                    <button type="submit" class="btn btn-secondary btn-sm">
                        <x-lucide-filter class="icon-sm ms-1"/> تصفية
                    </button>
                     @if(request()->hasAny(['search', 'currency_id', 'is_active']))
                        <a href="{{ route('admin.countries.index') }}" class="btn btn-link btn-sm ml-2">مسح الفلاتر</a>
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
                            <th style="width:5%">العلم</th>
                            <th>الاسم</th>
                            <th>ISO2</th>
                            <th>رمز الهاتف</th>
                            <th>العملة</th>
                            <th>المنطقة الزمنية</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($countries as $country)
                            <tr>
                                <td class="text-center">
                                    @if($country->flag_image_url)
                                        <img src="{{ $country->flag_image_url }}" alt="علم {{ $country->name }}" class="table-flag-preview">
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td><strong>{{ $country->name }}</strong></td>
                                <td>{{ $country->iso2 }}</td>
                                <td>{{ $country->phone_code ?? '-' }}</td>
                                <td>{{ $country->currency->code ?? 'غير محدد' }} <small class="text-muted">({{ $country->currency->symbol ?? '' }})</small></td>
                                <td>{{ $country->timezone->name ?? 'غير محدد' }}</td> {{-- Assuming timezone relation and name attribute --}}
                                <td>
                                      @if($country->is_active)
                                        <span class="status-badge status-badge-active">فعالة</span>
                                    @else
                                        <span class="status-badge status-badge-inactive">غير فعالة</span>
                                    @endif
                                </td>
                                <td class="actions ws-nowrap">
                                    <a href="{{ route('admin.countries.edit', $country->id) }}" class="btn btn-sm btn-outline-primary" title="تعديل">
                                        <x-lucide-pencil />
                                    </a>
                                    <form action="{{ route('admin.countries.destroy', $country->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل أنت متأكد؟ تحقق من المدن والبيانات المرتبطة أولاً.');">
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
                                <td colspan="8" class="text-center py-4">لم يتم العثور على دول.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($countries->hasPages())
            <div class="card-footer">
                 {{ $countries->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection

{{-- Select2 includes --}}
@push('styles')
    {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" /> --}}
@endpush
@push('scripts')
    {{-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script> --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
    <script>
        // document.addEventListener('DOMContentLoaded', function() {
        //     if (typeof $ !== 'undefined' && typeof $.fn.select2 === 'function') {
        //         $('.select2').each(function() { $(this).select2({ theme: "bootstrap-5", placeholder: "اختر...", width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style', }); });
        //     }
        // });
    </script>
@endpush