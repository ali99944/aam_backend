{{-- resources/views/admin/seo/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'إدارة SEO للصفحات - متجر AAM')

@push('styles')
<style>
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
        <h1>إدارة إعدادات SEO للصفحات</h1>
        <div class="actions">
            <a href="{{ route('admin.seo.create') }}" class="btn btn-primary">
                <x-lucide-plus class="icon-sm ms-2"/> إضافة SEO لصفحة جديدة
            </a>
        </div>
    </div>

    {{-- Search Form --}}
    <div class="card mb-4">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.seo.index') }}" class="form-inline flex-wrap">
                <div class="form-group mr-2 mb-2">
                     <label for="search" class="mr-1 d-none d-sm-inline">بحث:</label>
                    <input type="text" id="search" name="search" class="form-control form-control-sm" placeholder="بحث بالاسم، المفتاح، العنوان..." value="{{ request('search') }}">
                </div>
                <div class="form-group mb-2">
                    <button type="submit" class="btn btn-secondary btn-sm">
                        <x-lucide-search class="icon-sm ms-1"/> بحث
                    </button>
                     @if(request('search'))
                        <a href="{{ route('admin.seo.index') }}" class="btn btn-link btn-sm ml-2">مسح البحث</a>
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
                            <th>الاسم الإداري</th>
                            <th>مفتاح الصفحة</th>
                            <th>عنوان ميتا</th>
                            <th>وصف ميتا</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($seoPages as $seo)
                            <tr>
                                <td><strong>{{ $seo->name }}</strong></td>
                                <td><code>{{ $seo->key }}</code></td>
                                <td>{{ Str::limit($seo->title, 50) }}</td>
                                <td>{{ Str::limit($seo->description, 70) }}</td>
                                <td class="actions ws-nowrap">
                                    <a href="{{ route('admin.seo.edit', $seo->id) }}" class="btn btn-sm btn-outline-primary" title="تعديل">
                                        <x-lucide-pencil />
                                    </a>
                                    <form action="{{ route('admin.seo.destroy', $seo->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل أنت متأكد من حذف إعدادات SEO لمفتاح الصفحة \'{{ $seo->key }}\'؟');">
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
                                <td colspan="5" class="text-center py-4">لم يتم العثور على إعدادات SEO لأي صفحات.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
         @if ($seoPages->hasPages())
            <div class="card-footer">
                 {{ $seoPages->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection