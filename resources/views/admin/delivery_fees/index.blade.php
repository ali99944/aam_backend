@extends('layouts.admin')
@section('title', 'Manage Delivery Fees')

@push('styles')
<style>
.status-badge { font-size: 0.8em; padding: 3px 8px; border-radius: 5px; }
.status-badge-active { background-color: #d1e7dd; color: #0f5132; }
.status-badge-inactive { background-color: #e2e3e5; color: #41464b; }
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>Manage City Delivery Fees</h1>
        <div class="actions">
            <a href="{{ route('admin.delivery-fees.create') }}" class="btn btn-primary">
                <x-lucide-plus class="icon-sm mr-2"/> Add City Fee
            </a>
             {{-- Link to where default fee is managed (e.g., Settings) --}}
             {{-- <a href="{{ route('admin.settings.index') }}#delivery" class="btn btn-outline-secondary">Manage Default Fee</a> --}}
        </div>
    </div>

    {{-- Filter/Search Form --}}
    <div class="card mb-4">
        <div class="card-body py-2">
             <form method="GET" action="{{ route('admin.delivery-fees.index') }}" class="form-inline">
                 <div class="form-group mr-2">
                     <label for="search" class="mr-1 d-none d-sm-inline">Search City:</label>
                    <input type="text" id="search" name="search" class="form-control form-control-sm" placeholder="Enter city name..." value="{{ request('search') }}">
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
                    <a href="{{ route('admin.delivery-fees.index') }}" class="btn btn-link btn-sm ml-2">Clear</a>
                 @endif
            </form>
        </div>
    </div>

    <div class="card">
         <div class="card-header">
             <h3 class="card-title">City-Specific Fees</h3>
              {{-- Display Default Fee Here (fetch from settings) --}}
             {{-- <span class="float-end">Default Fee: <strong>AED {{ number_format(setting('default_delivery_fee', 10), 2) }}</strong></span> --}}
         </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>City</th>
                            <th>Fee Amount</th>
                            <th>Est. Delivery Time</th>
                            <th>Status</th>
                            <th>Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($deliveryFees as $fee)
                            <tr>
                                <td><strong>{{ $fee->city->name ?? 'N/A' }}</strong></td>
                                <td>{{ $fee->formatted_amount }}</td>
                                <td>{{ $fee->estimated_delivery_time ?? '-' }}</td>
                                <td>
                                     @if($fee->is_active)
                                        <span class="status-badge status-badge-active">Active</span>
                                    @else
                                        <span class="status-badge status-badge-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($fee->notes, 50) }}</td>
                                <td class="actions">
                                    <a href="{{ route('admin.delivery-fees.edit', $fee->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <x-lucide-pencil />
                                    </a>
                                    <form action="{{ route('admin.delivery-fees.destroy', $fee->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to remove the fee for {{ $fee->city->name ?? 'this city' }}?');">
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
                                <td colspan="6" class="text-center py-4">No city-specific delivery fees configured yet. The default fee will apply.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($deliveryFees->hasPages())
            <div class="card-footer">
                 {{ $deliveryFees->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection