@extends('layouts.admin')
@section('title', 'إدارة المدن')

@push('styles')
<style>
.status-badge { font-size: 0.8em; padding: 3px 8px; border-radius: 5px; font-weight: 500;} /* Added font-weight */
.status-badge-active { background-color: #d1e7dd; color: #0f5132; }
.status-badge-inactive { background-color: #e2e3e5; color: #41464b; }

/* RTL Adjustments for form-inline & icons if not globally handled */
html[dir="rtl"] .form-inline .form-group.mr-2 { margin-right: 0 !important; margin-left: 0.5rem !important; }
html[dir="rtl"] .form-inline label.mr-1 { margin-right: 0 !important; margin-left: 0.25rem !important; }
html[dir="rtl"] .form-inline .btn-link.ml-1 { margin-left: 0 !important; margin-right: 0.25rem !important; }
html[dir="rtl"] .mr-1 { margin-right: 0 !important; margin-left: 0.25rem !important; }
html[dir="rtl"] .mr-2 { margin-right: 0 !important; margin-left: 0.5rem !important; }
html[dir="rtl"] .ms-1 { margin-left: 0 !important; margin-right: 0.25rem !important; }
html[dir="rtl"] .ms-2 { margin-left: 0 !important; margin-right: 0.5rem !important; }
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>إدارة المدن</h1>
        <div class="actions">
            <a href="{{ route('admin.cities.create') }}" class="btn btn-primary">
                <x-lucide-plus class="icon-sm ms-2"/> إضافة مدينة جديدة {{-- ms-2 for RTL --}}
            </a>
        </div>
    </div>

    {{-- Filter/Search Form --}}
     <div class="card mb-4">
        <div class="card-body py-2">
             <form method="GET" action="{{ route('admin.cities.index') }}" class="form-inline flex-wrap">
                 <div class="form-group mr-2 mb-2">
                     <label for="search" class="mr-1 d-none d-sm-inline">بحث:</label>
                    <input type="text" id="search" name="search" class="form-control form-control-sm" placeholder="المدينة، الولاية، الدولة..." value="{{ request('search') }}">
                </div>
                 <div class="form-group mr-2 mb-2">
                    <label for="filter_country" class="mr-1">الدولة:</label>
                    <select id="filter_country" name="country_id" class="form-control form-control-sm select2"> {{-- Add select2 if used --}}
                         <option value="">كل الدول</option>
                         @foreach($countries as $id => $name)
                            <option value="{{ $id }}" {{ request('country_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                         @endforeach
                    </select>
                </div>
                 <div class="form-group mr-2 mb-2">
                    <label for="filter_state" class="mr-1">الولاية/المحافظة:</label>
                    <select id="filter_state" name="state_id" class="form-control form-control-sm select2"> {{-- Add select2 if used --}}
                         <option value="">كل الولايات</option>
                         {{-- Consider populating this dynamically based on country filter via JS --}}
                         @foreach($states as $id => $name) {{-- Make sure $states is passed to the view --}}
                            <option value="{{ $id }}" {{ request('state_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                         @endforeach
                    </select>
                </div>
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
                     @if(request()->hasAny(['search', 'country_id', 'state_id', 'is_active']))
                        <a href="{{ route('admin.cities.index') }}" class="btn btn-link btn-sm ml-1">مسح الفلاتر</a>
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
                            <th>اسم المدينة</th>
                            <th>الولاية/المحافظة</th>
                            <th>الدولة</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cities as $city)
                            <tr>
                                <td><strong>{{ $city->name }}</strong></td>
                                <td>{{ $city->state->name ?? 'غير محدد' }}</td>
                                <td>{{ $city->country->name ?? 'غير محدد' }}</td>
                                <td>
                                      @if($city->is_active)
                                        <span class="status-badge status-badge-active">فعالة</span>
                                    @else
                                        <span class="status-badge status-badge-inactive">غير فعالة</span>
                                    @endif
                                </td>
                                <td class="actions ws-nowrap"> {{-- Added ws-nowrap --}}
                                    <a href="{{ route('admin.cities.edit', $city->id) }}" class="btn btn-sm btn-outline-primary" title="تعديل">
                                        <x-lucide-pencil />
                                    </a>
                                    <form action="{{ route('admin.cities.destroy', $city->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل أنت متأكد؟ تحقق من رسوم التوصيل والعناوين المرتبطة أولاً.');">
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
                                <td colspan="5" class="text-center py-4">لم يتم العثور على مدن.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($cities->hasPages())
            <div class="card-footer">
                 {{ $cities->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection

{{-- Select2 includes if needed --}}
@push('styles')
    {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" /> --}}
@endpush
@push('scripts')
    {{-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script> --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
    <script>
        // document.addEventListener('DOMContentLoaded', function() {
        //     $('.select2').select2({
        //         theme: "bootstrap-5", // Or your preferred theme
        //         placeholder: $(this).data('placeholder') || "اختر...",
        //         // width: 'style', // Adjust width automatically
        //     });
        // });
    </script>
@endpush