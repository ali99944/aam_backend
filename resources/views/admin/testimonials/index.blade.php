@extends('layouts.admin')
@section('title', 'إدارة التقييمات والآراء')

@push('styles')
<style>
.table-avatar-preview { height: 45px; width: 45px; object-fit: cover; border-radius: 50%; }
.star-rating .lucide-star { color: #f59e0b; } /* Amber color for stars */
.quote-text { max-width: 400px; }
.status-badge { font-size: 0.8em; padding: 3px 8px; border-radius: 5px; }
.status-badge-active { background-color: #d1e7dd; color: #0f5132; }
.status-badge-inactive { background-color: #e2e3e5; color: #41464b; }
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>إدارة تقييمات العملاء</h1>
        <div class="actions">
            <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary">
                <x-lucide-plus class="icon-sm mr-2"/> إضافة تقييم جديد
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width:5%">الصورة</th>
                            <th>الاسم والمنصب</th>
                            <th>التقييم (النص)</th>
                            <th class="text-center">النجوم</th>
                            <th>الحالة</th>
                            <th>الترتيب</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($testimonials as $testimonial)
                            <tr>
                                <td><img src="{{ $testimonial->avatar_url }}" alt="{{ $testimonial->name }}" class="table-avatar-preview"></td>
                                <td>
                                    <strong>{{ $testimonial->name }}</strong>
                                    @if($testimonial->title_or_company)<small class="d-block text-muted">{{ $testimonial->title_or_company }}</small>@endif
                                </td>
                                <td class="quote-text">"{{ Str::limit($testimonial->quote, 80) }}"</td>
                                <td class="text-center star-rating">
                                    @if($testimonial->rating)
                                        @for ($i = 0; $i < $testimonial->rating; $i++)
                                            <x-lucide-star class="icon-sm d-inline-block fill-current"/>
                                        @endfor
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                     @if($testimonial->is_active)
                                        <span class="status-badge status-badge-active">فعال</span>
                                    @else
                                        <span class="status-badge status-badge-inactive">غير فعال</span>
                                    @endif
                                </td>
                                <td>{{ $testimonial->sort_order }}</td>
                                <td class="actions">
                                    <a href="{{ route('admin.testimonials.edit', $testimonial->id) }}" class="btn btn-sm btn-outline-primary" title="تعديل"><x-lucide-pencil /></a>
                                    <form action="{{ route('admin.testimonials.destroy', $testimonial->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف"><x-lucide-trash-2 /></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4">لا توجد تقييمات لعرضها.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($testimonials->hasPages())<div class="card-footer">{{ $testimonials->links() }}</div>@endif
    </div>
@endsection