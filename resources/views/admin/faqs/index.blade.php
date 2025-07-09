{{-- resources/views/admin/faqs/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'إدارة الأسئلة الشائعة')

@push('styles')
<style>
.status-badge { font-size: 0.8em; padding: 3px 8px; border-radius: 5px; font-weight: 500; }
.status-badge-active { background-color: #d1e7dd; color: #0f5132; }
.status-badge-inactive { background-color: #e2e3e5; color: #41464b; }

/* RTL Adjustments if not global */
html[dir="rtl"] .form-inline .form-group.mr-2 { margin-right: 0 !important; margin-left: 0.5rem !important; }
html[dir="rtl"] .form-inline label.mr-1 { margin-right: 0 !important; margin-left: 0.25rem !important; }
html[dir="rtl"] .form-inline .btn-link.ml-1 { margin-left: 0 !important; margin-right: 0.25rem !important; }
html[dir="rtl"] .mr-1 { margin-right: 0 !important; margin-left: 0.25rem !important; }
html[dir="rtl"] .mr-2 { margin-right: 0 !important; margin-left: 0.5rem !important; }
html[dir="rtl"] .ms-1 { margin-left: 0 !important; margin-right: 0.25rem !important; }
html[dir="rtl"] .ms-2 { margin-left: 0 !important; margin-right: 0.5rem !important; }
.ws-nowrap { white-space: nowrap; }
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>إدارة الأسئلة الشائعة</h1>
        <div class="actions">
            <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary">
                <x-lucide-plus class="icon-sm ms-2"/> إضافة سؤال شائع
            </a>
            <a href="{{ route('admin.faq-categories.index') }}" class="btn btn-outline-secondary ms-2"> {{-- Added ms-2 for spacing --}}
                <x-lucide-folder-tree class="icon-sm ms-1"/> إدارة الفئات
            </a>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="card mb-4">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.faqs.index') }}" class="form-inline flex-wrap">
                <div class="form-group mr-2 mb-2">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="بحث في الأسئلة أو الإجابات..." value="{{ request('search') }}">
                </div>
                <div class="form-group mr-2 mb-2">
                    <select name="faq_category_id" class="form-select form-select-sm select2" data-placeholder="كل الفئات"> {{-- Added select2 and placeholder --}}
                        <option value="">كل الفئات</option>
                        <option value="uncategorized" {{ request('faq_category_id') === 'uncategorized' ? 'selected' : '' }}>غير مصنف</option>
                        @foreach($categories as $id => $name)
                            <option value="{{ $id }}" {{ request('faq_category_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mr-2 mb-2">
                    <select name="is_active" class="form-select form-select-sm">
                        <option value="all">كل الحالات</option>
                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>فعال</option>
                        <option value="0" {{ request('is_active') == '0' && request()->filled('is_active') ? 'selected' : '' }}>غير فعال</option>
                    </select>
                </div>
                <div class="form-group mb-2">
                    <button type="submit" class="btn btn-secondary btn-sm">
                        <x-lucide-filter class="icon-sm ms-1"/> تصفية
                    </button>
                    @if(request()->hasAny(['search', 'faq_category_id', 'is_active']))
                        <a href="{{ route('admin.faqs.index') }}" class="btn btn-link btn-sm ml-1">مسح الفلاتر</a>
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
                            <th>السؤال</th>
                            <th>الفئة</th>
                            <th>الحالة</th>
                            <th>ترتيب العرض</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($faqs as $faq)
                            <tr>
                                <td><strong>{{ Str::limit($faq->question, 70) }}</strong></td>
                                <td>{{ $faq->category->name ?? 'غير مصنف' }}</td>
                                <td class="text-center">
                                    @if($faq->is_active)
                                        <span class="status-badge status-badge-active" title="فعال"><x-lucide-check-circle /></span>
                                    @else
                                        <span class="status-badge status-badge-inactive" title="غير فعال"><x-lucide-x-circle /></span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $faq->display_order }}</td>
                                <td class="actions ws-nowrap">
                                    <a href="{{ route('admin.faqs.edit', $faq->id) }}" class="btn btn-sm btn-outline-primary" title="تعديل"><x-lucide-pencil /></a>
                                    <form action="{{ route('admin.faqs.destroy', $faq->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل أنت متأكد من حذف هذا السؤال؟');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف"><x-lucide-trash-2 /></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4">لم يتم العثور على أسئلة شائعة.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($faqs->hasPages()) <div class="card-footer">{{ $faqs->appends(request()->query())->links() }}</div> @endif
    </div>
@endsection

@push('styles')
    {{-- Select2 CSS if you use it for filters --}}
    {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" /> --}}
@endpush
@push('scripts')
    {{-- Select2 JS if you use it for filters --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script> --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
    <script>
        // document.addEventListener('DOMContentLoaded', function() {
        //     if (typeof $ !== 'undefined' && typeof $.fn.select2 === 'function') {
        //         $('.select2').each(function() {
        //             $(this).select2({
        //                 theme: "bootstrap-5",
        //                 placeholder: $(this).data('placeholder') || "اختر...",
        //                 dir: "rtl" // Ensure Select2 is RTL aware
        //             });
        //         });
        //     }
        // });
    </script>
@endpush