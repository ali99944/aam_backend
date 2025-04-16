@extends('layouts.admin')

@section('title', 'Manage Categories - AAM Store')

@section('content')
    <div class="content-header">
        <h1>Manage Categories</h1>
        <div class="actions">
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                <x-lucide-plus class="icon-sm mr-2"/> Add New Category
            </a>
        </div>
    </div>

    {{-- Search Form --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.categories.index') }}" class="form-inline">
                <div class="form-group mr-2">
                    <input type="text" name="search" class="form-control" placeholder="Search by name..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-secondary">
                    <x-lucide-search class="icon-sm mr-1"/> Search
                </button>
                 @if(request('search'))
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-link ml-2">Clear</a>
                 @endif
            </form>
        </div>
    </div>


    <div class="card">
        <div class="card-body p-0"> {{-- Remove padding if table takes full width --}}
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Icon</th> {{-- Added --}}
                            <th>Cover</th> {{-- Added --}}
                            <th>Name</th>
                            <th>Active</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>
                                    @if($category->icon_image_url)
                                        <img src="{{ $category->icon_image_url }}" alt="Icon" class="table-icon-preview">
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                 <td>
                                    @if($category->cover_image_url)
                                        <img src="{{ $category->cover_image_url }}" alt="Cover" class="table-cover-preview">
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $category->name }}</td>
                                {{-- Description removed for brevity, view on edit --}}
                                {{-- <td>{{ Str::limit($category->description, 50) }}</td> --}}
                                <td>
                                    @if ($category->is_active)
                                        <span class="badge status-approved">Yes</span>
                                    @else
                                        <span class="badge status-pending">No</span>
                                    @endif
                                </td>
                                {{-- Created At removed for brevity --}}
                                {{-- <td>{{ $category->created_at->format('Y-m-d H:i') }}</td> --}}
                                <td class="actions">
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <x-lucide-pencil />
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <x-lucide-trash-2 />
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No categories found.</td> {{-- Updated colspan --}}
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
         {{-- Pagination Links outside card-body if padding removed --}}
        @if ($categories->hasPages())
            <div class="card-footer">
                 {{ $categories->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection

{{-- Add styles for table image previews --}}
@push('styles')
<style>
.table-icon-preview {
    max-height: 30px;
    max-width: 30px; /* Ensure width is also limited */
    vertical-align: middle;
    background-color: #eee; /* Background for better visibility of SVGs */
    padding: 2px;
    border-radius: 3px;
}
.table-cover-preview {
    max-height: 35px;
    max-width: 70px; /* Limit width for cover */
    vertical-align: middle;
    border-radius: 3px;
    object-fit: cover; /* Ensure aspect ratio maintained */
}
.py-4 { padding-top: 1.5rem !important; padding-bottom: 1.5rem !important; } /* If not in common.css */
</style>
@endpush