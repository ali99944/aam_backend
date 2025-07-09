@extends('layouts.admin')
@section('title', 'إدارة البانرات')

@push('styles')
<style>
.table-banner-preview { height: 50px; width: 120px; object-fit: cover; border-radius: 3px; }
.status-badge { font-size: 0.8em; padding: 3px 8px; border-radius: 5px; }
.status-badge-active { background-color: #d1e7dd; color: #0f5132; }
.status-badge-inactive { background-color: #e2e3e5; color: #41464b; }
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>إدارة البانرات</h1>
        <div class="actions">
            <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
                <x-lucide-plus class="icon-sm mr-2"/> إضافة بانر جديد
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width:15%">الصورة</th>
                            <th>العنوان</th>
                            <th>رابط الزر</th>
                            <th>الترتيب</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($banners as $banner)
                            <tr>
                                <td><img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="table-banner-preview"></td>
                                <td><strong>{{ $banner->title }}</strong></td>
                                <td>
                                    @if($banner->button_url)
                                        <a href="{{ $banner->button_url }}" target="_blank">{{ $banner->button_text ?: $banner->button_url }}</a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $banner->sort_order }}</td>
                                <td>
                                     @if($banner->is_active)
                                        <span class="status-badge status-badge-active">فعال</span>
                                    @else
                                        <span class="status-badge status-badge-inactive">غير فعال</span>
                                    @endif
                                </td>
                                <td class="actions">
                                    <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-sm btn-outline-primary" title="تعديل"><x-lucide-pencil /></a>
                                    <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف"><x-lucide-trash-2 /></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-4">لا توجد بانرات لعرضها.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($banners->hasPages())<div class="card-footer">{{ $banners->links() }}</div>@endif
    </div>
@endsection