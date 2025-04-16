@extends('layouts.admin')
@section('title', 'Payment History')

@push('styles')
<style>
/* Status badge styles */
.status-badge { font-size: 0.8em; padding: 3px 8px; border-radius: 5px; font-weight: 500;}
.status-badge-pending { background-color: #fff3cd; color: #664d03; border: 1px solid #ffeeba;}
.status-badge-completed { background-color: #d1e7dd; color: #0f5132; }
.status-badge-failed { background-color: #f8d7da; color: #842029; }
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>Payment History</h1>
        {{-- Maybe add export button later --}}
    </div>

    {{-- Filter Form --}}
    <div class="card mb-4">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.payments.index') }}" class="form-inline flex-wrap">
                 {{-- Search --}}
                 <div class="form-group mr-2 mb-2">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Order#, Txn ID, Customer..." value="{{ request('search') }}">
                </div>
                 {{-- Status --}}
                 <div class="form-group mr-2 mb-2">
                    <select name="status" class="form-select form-select-sm">
                         <option value="all">All Statuses</option>
                         @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                         @endforeach
                    </select>
                </div>
                 {{-- Payment Method --}}
                 <div class="form-group mr-2 mb-2">
                    <select name="payment_method_id" class="form-select form-select-sm">
                         <option value="">All Methods</option>
                         @foreach($paymentMethods as $id => $name)
                            <option value="{{ $id }}" {{ request('payment_method_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                         @endforeach
                    </select>
                </div>
                {{-- Date Range --}}
                <div class="form-group mr-2 mb-2">
                     <label for="start_date" class="mr-1 d-none d-sm-inline">From:</label>
                     <input type="date" id="start_date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                </div>
                 <div class="form-group mr-2 mb-2">
                     <label for="end_date" class="mr-1 d-none d-sm-inline">To:</label>
                     <input type="date" id="end_date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                </div>
                {{-- Submit --}}
                <div class="form-group mb-2">
                    <button type="submit" class="btn btn-secondary btn-sm">
                        <x-lucide-filter class="icon-sm mr-1"/> Filter
                    </button>
                     @if(request()->hasAny(['search', 'status', 'payment_method_id', 'start_date', 'end_date']))
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-link btn-sm ml-1">Clear Filters</a>
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
                            <th>Payment ID</th>
                            <th>Order</th>
                            <th>Customer</th>
                            <th>Invoice</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Transaction ID</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            <tr>
                                <td>#{{ $payment->id }}</td>
                                <td><a href="{{ route('admin.orders.show', $payment->order_id) }}">#{{ $payment->order_id }}</a></td>
                                <td>{{ $payment->order->customer->name ?? 'N/A' }}</td>
                                <td>
                                    @if($payment->invoice)
                                        <a href="#">#{{ $payment->invoice->invoice_number }}</a> {{-- Link to invoice view later --}}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $payment->paymentMethod->name ?? 'N/A' }}</td>
                                <td><strong>AED {{ number_format($payment->amount, 2) }}</strong></td>
                                <td>
                                     <span class="status-badge status-badge-{{ $payment->status }}">
                                        {{ $statuses[$payment->status] ?? ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td><small>{{ $payment->transaction_id ?? '-' }}</small></td>
                                <td>{{ $payment->created_at->format('d M Y, H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">No payment records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($payments->hasPages())
            <div class="card-footer">
                 {{ $payments->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection