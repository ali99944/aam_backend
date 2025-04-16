@extends('layouts.admin')
@section('title', 'Manage Delivery Companies')

@push('styles')
<style>
.table-logo-preview { height: 40px; width: auto; max-width: 100px; object-fit: contain; vertical-align: middle; }
.status-badge { font-size: 0.8em; padding: 3px 8px; border-radius: 5px; }
.status-badge-active { background-color: #d1e7dd; color: #0f5132; }
.status-badge-inactive { background-color: #e2e3e5; color: #41464b; }
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>Manage Delivery Companies</h1>
        <div class="actions">
            <a href="{{ route('admin.delivery-companies.create') }}" class="btn btn-primary">
                <x-lucide-plus class="icon-sm mr-2"/> Add New Company
            </a>
        </div>
    </div>

    {{-- Filter/Search Form --}}
    <div class="card mb-4">
        <div class="card-body py-2">
             <form method="GET" action="{{ route('admin.delivery-companies.index') }}" class="form-inline">
                 <div class="form-group mr-2">
                     <label for="search" class="mr-1 d-none d-sm-inline">Search:</label>
                    <input type="text" id="search" name="search" class="form-control form-control-sm" placeholder="Name, email, phone..." value="{{ request('search') }}">
                </div>
                 <div class="form-group mr-2">
                    <label for="filter_active" class="mr-1">Status:</label>
                    <select id="filter_active" name="is_active" class="form-control form-control-sm">
                         <option value="all">All</option>
                         <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                         <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-secondary btn-sm">
                    <x-lucide-filter class="icon-sm mr-1"/> Filter / Search
                </button>
                 @if(request('search') || request('is_active') != 'all')
                    <a href="{{ route('admin.delivery-companies.index') }}" class="btn btn-link btn-sm ml-2">Clear</a>
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
                            <th style="width:10%">Logo</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($companies as $company)
                            <tr>
                                <td class="text-center">
                                    <img src="{{ $company->logo_url }}" alt="{{ $company->name }} Logo" class="table-logo-preview">
                                </td>
                                <td><strong>{{ $company->name }}</strong></td>
                                <td>
                                    @if($company->contact_phone)
                                        <div title="Phone"><x-lucide-phone class="icon-xs text-muted"/> {{ $company->contact_phone }}</div>
                                    @endif
                                     @if($company->contact_email)
                                        <div title="Email"><x-lucide-mail class="icon-xs text-muted"/> {{ $company->contact_email }}</div>
                                    @endif
                                     @if(!$company->contact_phone && !$company->contact_email)
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                     @if($company->is_active)
                                        <span class="status-badge status-badge-active">Active</span>
                                    @else
                                        <span class="status-badge status-badge-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td class="actions">
                                    <a href="{{ route('admin.delivery-companies.edit', $company->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <x-lucide-pencil />
                                    </a>
                                    <form action="{{ route('admin.delivery-companies.destroy', $company->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure? Check if this company has pending orders first.');">
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
                                <td colspan="5" class="text-center py-4">No delivery companies found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($companies->hasPages())
            <div class="card-footer">
                 {{ $companies->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection