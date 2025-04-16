@extends('layouts.admin')
@section('title', 'Manage FAQ Categories')
@push('styles') <style> /* Status badge styles */ </style> @endpush
@section('content')
    <div class="content-header">
        <h1>Manage FAQ Categories</h1>
        <div class="actions"><a href="{{ route('admin.faq-categories.create') }}" class="btn btn-primary"><x-lucide-plus class="icon-sm mr-2"/> Add Category</a></div>
    </div>
    {{-- Search Form --}}
    <div class="card mb-4"><div class="card-body py-2"> <form method="GET" action="{{ route('admin.faq-categories.index') }}" class="form-inline"> ... </form></div></div>
    <div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="admin-table">
        <thead><tr><th>Name</th><th>Status</th><th>Order</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse ($categories as $category)
                <tr>
                    <td><strong>{{ $category->name }}</strong></td>
                    <td>{{-- Status Badge --}}</td>
                    <td>{{ $category->display_order }}</td>
                    <td class="actions">
                        <a href="{{ route('admin.faq-categories.edit', $category->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><x-lucide-pencil /></a>
                        <form action="{{ route('admin.faq-categories.destroy', $category->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('...');"> @csrf @method('DELETE') <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><x-lucide-trash-2 /></button></form>
                    </td>
                </tr>
            @empty <tr><td colspan="4" class="text-center py-4">No categories found.</td></tr> @endforelse
        </tbody>
    </table></div></div>
    @if ($categories->hasPages()) <div class="card-footer">{{ $categories->appends(request()->query())->links() }}</div> @endif
</div>
@endsection