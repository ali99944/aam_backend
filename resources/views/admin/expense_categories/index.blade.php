{{-- resources/views/admin/expense_categories/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'إدارة فئات المصروفات')

@push('styles')
<style>
.status-badge { font-size: 0.8em; padding: 3px 8px; border-radius: 5px; font-weight: 500; } /* Added font-weight */
.status-badge-active { background-color: #d1e7dd; color: #0f5132; }
.status-badge-inactive { background-color: #e2e3e5; color: #41464b; }

/* RTL Adjustments if not global */
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
        <h1>إدارة فئات المصروفات</h1>
        <div class="actions">
            <a href="{{ route('admin.expense-categories.create') }}" class="btn btn-primary">
                <x-lucide-plus class="icon-sm ms-2"/> إضافة فئة جديدة
            </a>
            <a href="{{ route('admin.expenses.index') }}" class="btn btn-outline-secondary ms-2">
                <x-lucide-receipt class="icon-sm ms-1"/> إدارة المصروفات
            </a>
        </div>
    </div>

    {{-- Optional Search Form --}}
    <div class="card mb-4">
        <div class="card-body py-2">
             <form method="GET" action="{{ route('admin.expense-categories.index') }}" class="form-inline flex-wrap">
                 <div class="form-group mr-2 mb-2">
                     <label for="search" class="mr-1 d-none d-sm-inline">بحث:</label>
                    <input type="text" id="search" name="search" class="form-control form-control-sm" placeholder="بحث بالاسم..." value="{{ request('search') }}">
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
                        <x-lucide-search class="icon-sm ms-1"/> بحث
                    </button>
                     @if(request('search') || (request()->filled('is_active') && request('is_active') != 'all') )
                        <a href="{{ route('admin.expense-categories.index') }}" class="btn btn-link btn-sm ml-2">مسح البحث</a>
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
                            <th>الاسم</th>
                            <th>الوصف</th>
                            <th>الحالة</th>
                            {{-- <th>ترتيب العرض</th> --}} {{-- Display order was in migration but not shown here --}}
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td><strong>{{ $category->name }}</strong></td>
                                <td>{{ Str::limit($category->description, 80) }}</td>
                                <td class="text-center">
                                    @if($category->is_active)
                                        <span class="status-badge status-badge-active" title="فعالة"><x-lucide-check-circle /></span>
                                    @else
                                        <span class="status-badge status-badge-inactive" title="غير فعالة"><x-lucide-x-circle /></span>
                                    @endif
                                </td>
                                {{-- <td class="text-center">{{ $category->display_order }}</td> --}}
                                <td class="actions ws-nowrap">
                                    <a href="{{ route('admin.expense-categories.edit', $category->id) }}" class="btn btn-sm btn-outline-primary" title="تعديل">
                                        <x-lucide-pencil />
                                    </a>
                                    <form action="{{ route('admin.expense-categories.destroy', $category->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل أنت متأكد؟ قد يؤدي حذف هذه الفئة إلى فشل إذا كانت مرتبطة بمصروفات.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                            <x-lucide-trash-2 class="icon-sm" />
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">لم يتم العثور على فئات للمصروفات.</td> {{-- Adjusted colspan --}}
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
         @if ($categories->hasPages())
            <div class="card-footer">
                 {{ $categories->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection