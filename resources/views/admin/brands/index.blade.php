@extends('layouts.admin')

@section('title', 'إدارة العلامات التجارية - متجر AAM')

{{-- Add specific styles for table image previews --}}
@push('styles')
<style>
.table-brand-image-preview {
    max-height: 40px;
    max-width: 80px; /* السماح بنسبة عرض إلى ارتفاع أوسع للشعارات */
    vertical-align: middle;
    border-radius: 3px;
    object-fit: contain; /* استخدام contain لرؤية الشعار بالكامل */
    background-color: #f8f9fa; /* خلفية فاتحة للصور الشفافة */
    border: 1px solid #eee;
}
.py-4 { padding-top: 1.5rem !important; padding-bottom: 1.5rem !important; } /* Utility class */
/* .form-control-sm { height: calc(1.5em + .5rem + 2px); padding: .25rem .5rem; font-size: .875em; border-radius: .2rem; } */ /* If not in forms.css */
/* .btn-sm { padding: .25rem .5rem; font-size: .875em; border-radius: .2rem; } */ /* If not in admin.css */

/* RTL Adjustments for form-inline */
html[dir="rtl"] .form-inline .form-group.mr-2 {
    margin-right: 0 !important;
    margin-left: 0.5rem !important; /* Use ml-2 for Bootstrap 5 logical properties */
}
html[dir="rtl"] .form-inline label.mr-1 {
    margin-right: 0 !important;
    margin-left: 0.25rem !important;
}
html[dir="rtl"] .form-inline .btn-link.ml-2 {
    margin-left: 0 !important;
    margin-right: 0.5rem !important;
}
html[dir="rtl"] .mr-1 { margin-right: 0 !important; margin-left: 0.25rem !important; }
html[dir="rtl"] .mr-2 { margin-right: 0 !important; margin-left: 0.5rem !important; }
html[dir="rtl"] .ms-1 { margin-left: 0 !important; margin-right: 0.25rem !important; } /* For icon placement */
html[dir="rtl"] .ms-2 { margin-left: 0 !important; margin-right: 0.5rem !important; } /* For icon placement */

/* Ensure table actions are on one line in RTL */
.ws-nowrap { white-space: nowrap; }
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>إدارة العلامات التجارية</h1>
        <div class="actions">
            <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">
                <x-lucide-plus class="icon-sm ms-2"/> إضافة علامة تجارية جديدة {{-- Changed mr-2 to ms-2 for RTL --}}
            </a>
        </div>
    </div>

    {{-- Search Form --}}
    <div class="card mb-4">
        <div class="card-body py-2"> {{-- Reduced padding for search bar card --}}
            <form method="GET" action="{{ route('admin.brands.index') }}" class="form-inline">
                <div class="form-group mr-2"> {{-- mr-2 will be flipped by CSS if using Bootstrap RTL --}}
                    <label for="search" class="mr-1 d-none d-sm-inline">بحث:</label>
                    <input type="text" id="search" name="search" class="form-control " placeholder="بحث بالاسم..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-secondary btn-sm">
                    <x-lucide-search class="icon-sm ms-1"/> بحث {{-- Changed mr-1 to ms-1 for RTL --}}
                </button>
                 @if(request('search'))
                    <a href="{{ route('admin.brands.index') }}" class="btn btn-link btn-sm ml-2">مسح البحث</a> {{-- ml-2 will be flipped by CSS if using Bootstrap RTL --}}
                 @endif
            </form>
        </div>
    </div>


    <div class="card">
        <div class="card-body p-0"> {{-- Remove padding as table handles it --}}
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 10%;">الشعار</th>
                            <th>الاسم</th>
                            <th style="width: 15%;">تاريخ الإنشاء</th>
                            <th style="width: 15%;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($brands as $brand)
                            <tr>
                                <td class="text-center"> {{-- Added text-center for better logo alignment --}}
                                    @if($brand->image_url)
                                        <img src="{{ $brand->image_url }}" alt="شعار {{ $brand->name }}" class="table-brand-image-preview">
                                    @else
                                        <span class="text-muted">لا يوجد شعار</span>
                                    @endif
                                </td>
                                <td><strong>{{ $brand->name }}</strong></td>
                                <td>{{ $brand->created_at->locale('ar')->translatedFormat('d M Y') }}</td> {{-- Arabic date format --}}
                                <td class="actions ws-nowrap"> {{-- Added ws-nowrap --}}
                                    {{-- Edit Button --}}
                                    <a href="{{ route('admin.brands.edit', $brand->id) }}" class="btn btn-sm btn-outline-primary" title="تعديل">
                                        <x-lucide-pencil />
                                    </a>
                                    {{-- Delete Form/Button --}}
                                    <form action="{{ route('admin.brands.destroy', $brand->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل أنت متأكد من حذف العلامة التجارية \'{{ $brand->name }}\'؟ لا يمكن التراجع عن هذا الإجراء.');">
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
                                <td colspan="4" class="text-center py-4">لم يتم العثور على علامات تجارية تطابق بحثك.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{-- Pagination Links --}}
        @if ($brands->hasPages())
            <div class="card-footer">
                 {{ $brands->appends(request()->query())->links() }} {{-- Maintain search query on pagination --}}
            </div>
        @endif
    </div>
@endsection