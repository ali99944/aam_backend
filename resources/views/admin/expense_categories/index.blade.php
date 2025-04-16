@extends('layouts.admin')
@section('title', 'Manage Expense Categories')

@push('styles')
<style>
.status-badge { font-size: 0.8em; padding: 3px 8px; border-radius: 5px; }
.status-badge-active { background-color: #d1e7dd; color: #0f5132; }
.status-badge-inactive { background-color: #e2e3e5; color: #41464b; }
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>Manage Expense Categories</h1>
        <div class="actions">
            <a href="{{ route('admin.expense-categories.create') }}" class="btn btn-primary">
                <x-lucide-plus class="icon-sm mr-2"/> Add New Category
            </a>
        </div>
    </div>

    {{-- Optional Search Form --}}
    <div class="card mb-4">
        <div class="card-body py-2">
             <form method="GET" action="{{ route('admin.expense-categories.index') }}" class="form-inline">
                 <div class="form-group mr-2">
                     <label for="search" class="mr-1 d-none d-sm-inline">Search:</label>
                    <input type="text" id="search" name="search" class="form-control form-control-sm" placeholder="Search name..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-secondary btn-sm">
                    <x-lucide-search class="icon-sm mr-1"/> Search
                </button>
                 @if(request('search'))
                    <a href="{{ route('admin.expense-categories.index') }}" class="btn btn-link btn-sm ml-2">Clear</a>
                 @endif
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td><strong>{{ $category->name }}</strong></td>
                                <td>{{ Str::limit($category->description, 80) }}</td>
                                <td>
                                    @if($category->is_active)
                                        <span class="status-badge status-badge-active">Active</span>
                                    @else
                                        <span class="status-badge status-badge-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td class="actions">
                                    <a href="{{ route('admin.expense-categories.edit', $category->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <x-lucide-pencil />
                                    </a>
                                    <form action="{{ route('admin.expense-categories.destroy', $category->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure? Deleting this category might fail if it has expenses linked to it.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <x-lucide-trash-2 class="icon-sm" />
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">No expense categories found.</td>
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