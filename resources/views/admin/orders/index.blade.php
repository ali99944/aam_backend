@extends('layouts.admin')
@section('title', 'Manage Orders')

@push('styles')
<style>
/* Add status badge styles if not globally available */
.status-badge { font-size: 0.8em; padding: 3px 8px; border-radius: 5px; font-weight: 500;}
.status-badge-pending { background-color: #fff3cd; color: #664d03; border: 1px solid #ffeeba;}
.status-badge-processing { background-color: #cff4fc; color: #055160; border: 1px solid #b6effb;}
.status-badge-completed { background-color: #d1e7dd; color: #0f5132; }
.status-badge-cancelled { background-color: #f8d7da; color: #842029; }
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>Manage Orders</h1>
        {{-- No "Add Order" button usually for admin, orders come from storefront --}}
    </div>

    {{-- Filter Form --}}
    <div class="card mb-4">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="form-inline flex-wrap">
                 {{-- Search --}}
                 <div class="form-group mr-2 mb-2">
                     <label for="search" class="mr-1 d-none d-sm-inline">Search:</label>
                    <input type="text" id="search" name="search" class="form-control form-control-sm" placeholder="Order ID, Name, Email..." value="{{ request('search') }}">
                </div>
                 {{-- Status --}}
                 <div class="form-group mr-2 mb-2">
                    <label for="filter_status" class="mr-1">Status:</label>
                    <select id="filter_status" name="status" class="form-control form-control-sm">
                         <option value="all">All Statuses</option>
                         @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                         @endforeach
                    </select>
                </div>
                {{-- Date Range --}}
                <div class="form-group mr-2 mb-2">
                     <label for="start_date" class="mr-1">From:</label>
                     <input type="date" id="start_date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                </div>
                 <div class="form-group mr-2 mb-2">
                     <label for="end_date" class="mr-1">To:</label>
                     <input type="date" id="end_date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                </div>
                {{-- Submit --}}
                <div class="form-group mb-2">
                    <button type="submit" class="btn btn-secondary btn-sm">
                        <x-lucide-filter class="icon-sm mr-1"/> Filter
                    </button>
                     @if(request()->hasAny(['search', 'status', 'start_date', 'end_date']))
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-link btn-sm ml-1">Clear Filters</a>
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
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td><strong>#{{ $order->id }}</strong></td>
                                <td>
                                    {{ $order->customer->name ?? 'N/A' }}
                                    <small class="d-block text-muted">{{ $order->customer->email ?? '' }}</small>
                                </td>
                                <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                                <td>{{ $order->formatted_total }}</td>
                                <td>{{ $order->payment_method }}</td> {{-- Could show payment status from relationship --}}
                                <td>
                                     <span class="status-badge status-badge-{{ str_replace('_','-',$order->status) }}">
                                        {{ $statuses[$order->status] ?? ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="actions">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-info" title="View Details">
                                        <x-lucide-eye />
                                    </a>
                                     {{-- Allow delete only for certain statuses? --}}
                                     @if(!in_array($order->status, ['completed', 'processing']))
                                        <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('DELETE this order? This is usually not recommended.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Order">
                                                <x-lucide-trash-2 />
                                            </button>
                                        </form>
                                     @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No orders found matching your criteria.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($orders->hasPages())
            <div class="card-footer">
                 {{ $orders->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection