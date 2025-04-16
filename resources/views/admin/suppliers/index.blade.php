@extends('layouts.admin')
@section('title', 'Manage Suppliers')

@push('styles')
<style>
.table-logo-preview { height: 40px; width: auto; max-width: 100px; object-fit: contain; vertical-align: middle; background: #fff; border: 1px solid #eee; padding: 2px; border-radius: 3px;}
/* Status badge styles */
.status-badge { font-size: 0.8em; padding: 3px 8px; border-radius: 5px; }
.status-badge-active { background-color: #d1e7dd; color: #0f5132; }
.status-badge-inactive { background-color: #e2e3e5; color: #41464b; }
.balance-owed { color: var(--danger-color); font-weight: 500; }
.balance-credit { color: var(--success-color); font-weight: 500; }
.contact-info div { margin-bottom: 2px; font-size: 0.9em;}
.contact-info .lucide { margin-right: 4px; vertical-align: middle; width: 14px; height: 14px; }
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>Manage Suppliers</h1>
        <div class="actions">
            <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">
                <x-lucide-plus class="icon-sm mr-2"/> Add New Supplier
            </a>
        </div>
    </div>

    {{-- Filter/Search Form --}}
     <div class="card mb-4">
        <div class="card-body py-2">
             <form method="GET" action="{{ route('admin.suppliers.index') }}" class="form-inline flex-wrap">
                 <div class="form-group mr-2 mb-2">
                     <label for="search" class="mr-1 d-none d-sm-inline">Search:</label>
                    <input type="text" id="search" name="search" class="form-control form-control-sm" placeholder="Name, Contact, Email, Phone..." value="{{ request('search') }}">
                </div>
                 <div class="form-group mr-2 mb-2">
                    <label for="filter_active" class="mr-1">Status:</label>
                    <select id="filter_active" name="is_active" class="form-control form-control-sm">
                         <option value="all">All</option>
                         <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                         <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="form-group mb-2">
                    <button type="submit" class="btn btn-secondary btn-sm">
                        <x-lucide-filter class="icon-sm mr-1"/> Filter
                    </button>
                     @if(request()->hasAny(['search', 'is_active']))
                        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-link btn-sm ml-1">Clear Filters</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width:5%">Logo</th>
                            <th>Supplier Name</th>
                            <th>Contact</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($suppliers as $supplier)
                            <tr>
                                <td class="text-center">
                                    <img src="{{ $supplier->image_url }}" alt="{{ $supplier->name }}" class="table-logo-preview">
                                </td>
                                <td>
                                    <strong>{{ $supplier->name }}</strong>
                                    @if($supplier->website)
                                        <a href="{{ $supplier->website }}" target="_blank" title="Visit Website">
                                            <x-lucide-external-link class="icon-xs text-muted ms-1"/>
                                        </a>
                                    @endif
                                </td>
                                <td class="contact-info">
                                    @if($supplier->contact_person)
                                        <div><x-lucide-user class="text-muted"/> {{ $supplier->contact_person }}</div>
                                    @endif
                                    @if($supplier->email)
                                        <div><x-lucide-mail class="text-muted"/> {{ $supplier->email }}</div>
                                    @endif
                                    @if($supplier->phone)
                                        <div><x-lucide-phone class="text-muted"/> {{ $supplier->phone }}</div>
                                    @endif
                                </td>
                                <td class="{{ ($supplier->balance ?? 0) == 0 ? '' : (($supplier->balance ?? 0) > 0 ? 'balance-owed' : 'balance-credit') }}">
                                    {{ $supplier->formatted_balance }}
                                </td>
                                <td>
                                     @if($supplier->is_active)
                                        <span class="status-badge status-badge-active">Active</span>
                                    @else
                                        <span class="status-badge status-badge-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td class="actions">
                                    {{-- Add Button to Manage Balance/Payments later --}}
                                    {{-- <a href="#" class="btn btn-sm btn-outline-success" title="Manage Balance/Payments"><x-lucide-dollar-sign /></a> --}}
                                    <a href="{{ route('admin.suppliers.edit', $supplier->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <x-lucide-pencil />
                                    </a>
                                    <form action="{{ route('admin.suppliers.destroy', $supplier->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure? Check balance and related purchase orders first.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <x-lucide-trash-2 />
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-4">No suppliers found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($suppliers->hasPages())
            <div class="card-footer">
                 {{ $suppliers->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection