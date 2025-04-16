@extends('layouts.admin')
@section('title', 'Manage Delivery Personnel')

@push('styles')
<style>
.table-avatar-preview { height: 45px; width: 45px; object-fit: cover; border-radius: 50%; border: 1px solid #eee; }
.status-badge { font-size: 0.8em; padding: 3px 8px; border-radius: 5px; }
.status-badge-active { background-color: #d1e7dd; color: #0f5132; }
.status-badge-inactive { background-color: #e2e3e5; color: #41464b; }
.contact-info div { margin-bottom: 2px; }
.contact-info .lucide { margin-right: 4px; vertical-align: middle; }
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>Manage Delivery Personnel</h1>
        <div class="actions">
            <a href="{{ route('admin.delivery-personnel.create') }}" class="btn btn-primary">
                <x-lucide-user-plus class="icon-sm mr-2"/> Add New Person
            </a>
        </div>
    </div>

    {{-- Filter Form --}}
     <div class="card mb-4">
        <div class="card-body py-2">
             <form method="GET" action="{{ route('admin.delivery-personnel.index') }}" class="form-inline flex-wrap">
                 <div class="form-group mr-2 mb-2">
                     <label for="search" class="mr-1 d-none d-sm-inline">Search:</label>
                    <input type="text" id="search" name="search" class="form-control form-control-sm" placeholder="Name, email, phone..." value="{{ request('search') }}">
                </div>
                 <div class="form-group mr-2 mb-2">
                    <label for="filter_company" class="mr-1">Company:</label>
                    <select id="filter_company" name="delivery_company_id" class="form-control form-control-sm">
                         <option value="">All</option>
                         <option value="independent" {{ request('delivery_company_id') === 'independent' ? 'selected' : '' }}>Independent</option>
                         @foreach($companies as $id => $name)
                            <option value="{{ $id }}" {{ request('delivery_company_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                         @endforeach
                    </select>
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
                     @if(request()->hasAny(['search', 'delivery_company_id', 'is_active']))
                        <a href="{{ route('admin.delivery-personnel.index') }}" class="btn btn-link btn-sm ml-1">Clear Filters</a>
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
                            <th style="width:5%">Avatar</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Company</th>
                            <th>Vehicle</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($deliveryPersonnel as $person)
                            <tr>
                                <td class="text-center">
                                    <img src="{{ $person->avatar_url }}" alt="{{ $person->name }}" class="table-avatar-preview">
                                </td>
                                <td><strong>{{ $person->name }}</strong></td>
                                <td class="contact-info">
                                    <div><x-lucide-mail class="icon-xs text-muted"/> {{ $person->email }}</div>
                                    <div><x-lucide-phone class="icon-xs text-muted"/> {{ $person->phone }}</div>
                                </td>
                                <td>{{ $person->deliveryCompany->name ?? 'N/A' }}</td> {{-- Uses withDefault --}}
                                <td>
                                    @if($person->vehicle_type || $person->vehicle_plate_number)
                                        {{ $person->vehicle_type ?? 'N/A' }}
                                        @if($person->vehicle_plate_number)
                                            <small class="d-block text-muted">({{ $person->vehicle_plate_number }})</small>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                      @if($person->is_active)
                                        <span class="status-badge status-badge-active">Active</span>
                                    @else
                                        <span class="status-badge status-badge-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td class="actions">
                                    <a href="{{ route('admin.delivery-personnel.edit', $person->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <x-lucide-pencil />
                                    </a>
                                    <form action="{{ route('admin.delivery-personnel.destroy', $person->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure? Check for active orders first.');">
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
                                <td colspan="7" class="text-center py-4">No delivery personnel found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($deliveryPersonnel->hasPages())
            <div class="card-footer">
                 {{ $deliveryPersonnel->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection