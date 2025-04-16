@extends('layouts.admin')

@section('title', 'Manage Discounts - AAM Store')

@push('styles')
<style>
/* Custom badge colors */
.badge.status-active { background-color: var(--success-color); color: white; }
.badge.status-inactive { background-color: var(--secondary-color); color: white; }
.badge.status-expired { background-color: var(--danger-color); color: white; }
.badge.type-fixed { background-color: var(--info-color); color: white; }
.badge.type-percentage { background-color: var(--warning-color); color: #333; }
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>Manage Discounts</h1>
        <div class="actions">
            <a href="{{ route('admin.discounts.create') }}" class="btn btn-primary">
                <x-lucide-plus class="icon-sm mr-2"/> Add New Discount
            </a>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="card mb-4">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.discounts.index') }}" class="form-inline">
                 <div class="form-group mr-2">
                    <label for="filter_status" class="mr-1">Status:</label>
                    <select id="filter_status" name="status" class="form-control form-control-sm">
                         <option value="all">All Statuses</option>
                         @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                         @endforeach
                    </select>
                </div>
                 <div class="form-group mr-2">
                    <label for="filter_type" class="mr-1">Type:</label>
                    <select id="filter_type" name="type" class="form-control form-control-sm">
                         <option value="all">All Types</option>
                         @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                         @endforeach
                    </select>
                </div>
                <div class="form-group mr-2">
                     <label for="search" class="mr-1 d-none d-sm-inline">Search:</label>
                    <input type="text" id="search" name="search" class="form-control form-control-sm" placeholder="Search name or code..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-secondary btn-sm">
                    <x-lucide-filter class="icon-sm mr-1"/> Filter
                </button>
                 @if(request('search') || request('status') != 'all' || request('type') != 'all')
                    <a href="{{ route('admin.discounts.index') }}" class="btn btn-link btn-sm ml-2">Clear Filters</a>
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
                            <th>Code</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Status</th>
                            <th>Expiration</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($discounts as $discount)
                            <tr>
                                <td><strong>{{ $discount->name }}</strong></td>
                                <td>{{ $discount->code ?? '-' }}</td>
                                <td>
                                    <span class="badge type-{{ $discount->type }}">{{ ucfirst($discount->type) }}</span>
                                </td>
                                <td>{{ $discount->formatted_value }}</td>
                                <td>
                                     <span class="badge status-{{ $discount->status }}">{{ ucfirst($discount->status) }}</span>
                                     {{-- Optionally show if technically valid now --}}
                                     {{-- @if($discount->is_valid) <x-lucide-check-circle class="text-success icon-sm" /> @endif --}}
                                </td>
                                <td>{{ $discount->expiration_details }}</td>
                                <td class="actions">
                                    <a href="{{ route('admin.discounts.edit', $discount->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <x-lucide-pencil />
                                    </a>
                                    <form action="{{ route('admin.discounts.destroy', $discount->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this discount?');">
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
                                <td colspan="7" class="text-center py-4">No discounts found matching your criteria.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($discounts->hasPages())
            <div class="card-footer">
                 {{ $discounts->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection