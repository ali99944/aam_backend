@extends('layouts.admin')

@section('title', 'Manage Sub Categories - AAM Store')

@push('styles')
<style>
/* Include styles from category index or move to common.css */
.table-icon-preview { max-height: 30px; max-width: 30px; vertical-align: middle; background-color: #eee; padding: 2px; border-radius: 3px; }
.table-cover-preview { max-height: 35px; max-width: 70px; vertical-align: middle; border-radius: 3px; object-fit: cover; }
.py-4 { padding-top: 1.5rem !important; padding-bottom: 1.5rem !important; }
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>Manage Sub Categories</h1>
        <div class="actions">
            <a href="{{ route('admin.subcategories.create') }}" class="btn btn-primary">
                <x-lucide-plus class="icon-sm mr-2"/> Add New Sub Category
            </a>
        </div>
    </div>

    {{-- Filter/Search Form --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.subcategories.index') }}" class="form-inline">
                 <div class="form-group mr-2">
                    <label for="filter_category" class="mr-1">Category:</label>
                    <select id="filter_category" name="category_id" class="form-control form-control-sm">
                         <option value="">All Categories</option>
                         @foreach($categories as $id => $name)
                            <option value="{{ $id }}" {{ request('category_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                         @endforeach
                    </select>
                </div>
                <div class="form-group mr-2">
                     <label for="search" class="mr-1 d-none d-md-inline">Search:</label>
                    <input type="text" id="search" name="search" class="form-control form-control-sm" placeholder="Search name..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-secondary btn-sm">
                    <x-lucide-search class="icon-sm mr-1"/> Filter / Search
                </button>
                 @if(request('search') || request('category_id'))
                    <a href="{{ route('admin.subcategories.index') }}" class="btn btn-link btn-sm ml-2">Clear</a>
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
                            <th>ID</th>
                            <th>Icon</th>
                            <th>Cover</th>
                            <th>Sub Category Name</th>
                            <th>Parent Category</th>
                            <th>Active</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($subCategories as $subCategory)
                            <tr>
                                <td>{{ $subCategory->id }}</td>
                                <td>
                                    @if($subCategory->icon_image_url)
                                        <img src="{{ $subCategory->icon_image_url }}" alt="Icon" class="table-icon-preview">
                                    @else <span class="text-muted">-</span> @endif
                                </td>
                                <td>
                                    @if($subCategory->cover_image_url)
                                        <img src="{{ $subCategory->cover_image_url }}" alt="Cover" class="table-cover-preview">
                                    @else <span class="text-muted">-</span> @endif
                                </td>
                                <td>{{ $subCategory->name }}</td>
                                <td>
                                    <a href="{{ route('admin.categories.edit', $subCategory->category_id) }}">
                                        {{ $subCategory->category->name ?? 'N/A' }}
                                    </a>
                                </td>
                                <td>
                                    @if ($subCategory->is_active)
                                        <span class="badge status-approved">Yes</span>
                                    @else
                                        <span class="badge status-pending">No</span>
                                    @endif
                                </td>
                                <td class="actions">
                                    <a href="{{ route('admin.subcategories.edit', $subCategory->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <x-lucide-pencil />
                                    </a>
                                    <form action="{{ route('admin.subcategories.destroy', $subCategory->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure? This cannot be undone.');">
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
                                <td colspan="7" class="text-center py-4">No sub categories found matching your criteria.</td> {{-- Updated colspan --}}
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($subCategories->hasPages())
            <div class="card-footer">
                 {{ $subCategories->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection