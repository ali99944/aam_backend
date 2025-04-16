@extends('layouts.admin')

@section('title', 'Manage Brands - AAM Store')

{{-- Add specific styles for table image previews --}}
@push('styles')
<style>
.table-brand-image-preview {
    max-height: 40px;
    max-width: 80px; /* Allow wider aspect ratio for logos */
    vertical-align: middle;
    border-radius: 3px;
    object-fit: contain; /* Use contain to see the whole logo */
    background-color: #f8f9fa; /* Light background for transparent images */
    border: 1px solid #eee;
}
.py-4 { padding-top: 1.5rem !important; padding-bottom: 1.5rem !important; } /* Utility class */
.form-control-sm { height: calc(1.5em + .5rem + 2px); padding: .25rem .5rem; font-size: .875em; border-radius: .2rem; } /* If not in forms.css */
.btn-sm { padding: .25rem .5rem; font-size: .875em; border-radius: .2rem; } /* If not in admin.css */
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>Manage Brands</h1>
        <div class="actions">
            <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">
                <x-lucide-plus class="icon-sm mr-2"/> Add New Brand
            </a>
        </div>
    </div>

    {{-- Search Form --}}
    <div class="card mb-4">
        <div class="card-body py-2"> {{-- Reduced padding for search bar card --}}
            <form method="GET" action="{{ route('admin.brands.index') }}" class="form-inline">
                <div class="form-group mr-2">
                    <label for="search" class="mr-1 d-none d-sm-inline">Search:</label>
                    <input type="text" id="search" name="search" class="form-control form-control-sm" placeholder="Search by name..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-secondary btn-sm">
                    <x-lucide-search class="icon-sm mr-1"/> Search
                </button>
                 @if(request('search'))
                    <a href="{{ route('admin.brands.index') }}" class="btn btn-link btn-sm ml-2">Clear</a>
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
                            <th style="width: 10%;">Image</th>
                            <th>Name</th>
                            <th style="width: 15%;">Created At</th>
                            <th style="width: 15%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($brands as $brand)
                            <tr>
                                <td>
                                    @if($brand->image_url)
                                        <img src="{{ $brand->image_url }}" alt="{{ $brand->name }} Logo" class="table-brand-image-preview">
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>
                                <td><strong>{{ $brand->name }}</strong></td>
                                <td>{{ $brand->created_at->format('d M Y') }}</td> {{-- Shorter date format --}}
                                <td class="actions">
                                    {{-- Edit Button --}}
                                    <a href="{{ route('admin.brands.edit', $brand->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <x-lucide-pencil />
                                    </a>
                                    {{-- Delete Form/Button --}}
                                    <form action="{{ route('admin.brands.destroy', $brand->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete the brand \'{{ $brand->name }}\'? This cannot be undone.');">
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
                                <td colspan="4" class="text-center py-4">No brands found matching your criteria.</td> {{-- Updated colspan --}}
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