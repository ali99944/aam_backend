@extends('layouts.admin')
@section('title', 'Manage FAQs')
@push('styles') <style> /* Status badge styles */ </style> @endpush
@section('content')
    <div class="content-header">
        <h1>Manage FAQs</h1>
        <div class="actions"><a href="{{ route('admin.faqs.create') }}" class="btn btn-primary"><x-lucide-plus class="icon-sm mr-2"/> Add FAQ</a></div>
    </div>
    {{-- Filter Form --}}
    <div class="card mb-4"><div class="card-body py-2"> <form method="GET" action="{{ route('admin.faqs.index') }}" class="form-inline flex-wrap"> ... </form></div></div> {{-- Add filters for category, status, search --}}
    <div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="admin-table">
        <thead><tr><th>Question</th><th>Category</th><th>Status</th><th>Order</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse ($faqs as $faq)
                <tr>
                    <td><strong>{{ Str::limit($faq->question, 80) }}</strong></td>
                    <td>{{ $faq->category->name ?? 'N/A' }}</td>
                    <td>{{-- Status Badge --}}</td>
                    <td>{{ $faq->display_order }}</td>
                    <td class="actions">
                        <a href="{{ route('admin.faqs.edit', $faq->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><x-lucide-pencil /></a>
                        <form action="{{ route('admin.faqs.destroy', $faq->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure?');"> @csrf @method('DELETE') <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><x-lucide-trash-2 /></button></form>
                    </td>
                </tr>
            @empty <tr><td colspan="5" class="text-center py-4">No FAQs found.</td></tr> @endforelse
        </tbody>
    </table></div></div>
    @if ($faqs->hasPages()) <div class="card-footer">{{ $faqs->appends(request()->query())->links() }}</div> @endif
</div>
@endsection