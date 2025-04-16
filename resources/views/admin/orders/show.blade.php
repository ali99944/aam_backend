@extends('layouts.admin')
@section('title', "Order Details #{$order->id}")

@push('styles')
<style>
.order-summary-table td { padding: 0.5rem 0.75rem; border-bottom: 1px solid #eee; }
.order-summary-table tr:last-child td { border-bottom: none; }
.item-image { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; margin-right: 10px;}
/* Status badge styles from index */
.status-badge { font-size: 0.8em; padding: 3px 8px; border-radius: 5px; font-weight: 500;}
.status-badge-pending { background-color: #fff3cd; color: #664d03; border: 1px solid #ffeeba;}
.status-badge-processing { background-color: #cff4fc; color: #055160; border: 1px solid #b6effb;}
.status-badge-completed { background-color: #d1e7dd; color: #0f5132; }
.status-badge-cancelled { background-color: #f8d7da; color: #842029; }
/* Payment status */
.payment-status-pending { color: #ffc107; }
.payment-status-completed { color: var(--success-color); }
.payment-status-failed { color: var(--danger-color); }
/* Delivery status */
.delivery-status-pending { color: var(--warning-color); }
.delivery-status-completed { color: var(--success-color); }
.delivery-status-failed { color: var(--danger-color); }
</style>
@endpush

@section('content')
    <div class="content-header">
        <div>
            <h1>Order #{{ $order->id }}</h1>
            <small class="text-muted">Placed on: {{ $order->created_at->format('d M Y, H:i A') }}</small>
        </div>
        <div class="actions">
            {{-- Print Invoice Button (Link to invoice generation route later) --}}
            @if($order->invoice)
                <a href="#" class="btn btn-outline-secondary"><x-lucide-printer class="icon-sm mr-1"/> Print Invoice (#{{ $order->invoice->invoice_number }})</a>
            @endif
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Back to Orders</a>
        </div>
    </div>

    <div class="row">
        {{-- Left Column: Order Items, Summary --}}
        <div class="col-lg-8">
             {{-- Order Items --}}
            <div class="card mb-4">
                <div class="card-header"><h3 class="card-title mb-0">Order Items</h3></div>
                <div class="card-body p-0">
                     <div class="table-responsive">
                        <table class="admin-table">
                             <thead><tr><th>Product</th><th>SKU</th><th class="text-center">Quantity</th><th class="text-end">Unit Price</th><th class="text-end">Total</th></tr></thead>
                             <tbody>
                                @foreach ($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                             <img src="{{ $item->product->main_image_url ?? asset('images/placeholder-product.png') }}" alt="{{ $item->product->name ?? 'N/A' }}" class="item-image">
                                             <span>{{ $item->product->name ?? 'Product Not Found' }}</span>
                                             {{-- Add variation/addon info here if applicable --}}
                                        </div>
                                    </td>
                                    <td>{{ $item->product->sku_code ?? 'N/A' }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">AED {{ number_format($item->price, 2) }}</td>
                                    <td class="text-end">AED {{ number_format($item->total, 2) }}</td>
                                </tr>
                                @endforeach
                             </tbody>
                        </table>
                     </div>
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="card mb-4">
                 <div class="card-header"><h3 class="card-title mb-0">Order Summary</h3></div>
                 <div class="card-body">
                     <table class="w-100 order-summary-table">
                        {{-- You should calculate subtotal properly when creating the order --}}
                        {{-- <tr><td>Subtotal:</td><td class="text-end">AED {{ number_format($order->subtotal ?? $order->total - $order->delivery_fee, 2) }}</td></tr> --}}
                         <tr><td>Delivery Fee:</td><td class="text-end">AED {{ number_format($order->delivery_fee, 2) }}</td></tr>
                         {{-- Add Discount row if applicable --}}
                         {{-- <tr><td>Discount:</td><td class="text-end text-danger">- AED {{ number_format($order->discount_amount ?? 0, 2) }}</td></tr> --}}
                         {{-- Add Tax row if applicable --}}
                         {{-- <tr><td>VAT (5%):</td><td class="text-end">AED {{ number_format($order->tax_amount ?? 0, 2) }}</td></tr> --}}
                         <tr><td><strong>Grand Total:</strong></td><td class="text-end"><strong>{{ $order->formatted_total }}</strong></td></tr>
                     </table>
                 </div>
            </div>
        </div>

        {{-- Right Column: Customer, Status, Payment, Delivery --}}
        <div class="col-lg-4">
            {{-- Customer Info --}}
            <div class="card mb-4">
                 <div class="card-header"><h3 class="card-title mb-0">Customer</h3></div>
                 <div class="card-body">
                     <p><strong>Name:</strong> {{ $order->customer->name ?? 'N/A' }}</p>
                     <p><strong>Email:</strong> {{ $order->customer->email ?? 'N/A' }}</p>
                     {{-- Add Phone, Address link etc. --}}
                     {{-- <p><strong>Phone:</strong> {{ $order->customer->phone ?? 'N/A' }}</p> --}}
                     {{-- <p><a href="#">View Shipping Address</a></p> --}}
                 </div>
            </div>

             {{-- Order Status Update --}}
             <div class="card mb-4">
                 <div class="card-header"><h3 class="card-title mb-0">Order Status</h3></div>
                 <div class="card-body">
                    <p>Current Status:
                         <span class="status-badge status-badge-{{ str_replace('_','-',$order->status) }}">
                            {{ $statuses[$order->status] ?? ucfirst($order->status) }}
                         </span>
                    </p>
                     {{-- Allow changing status only if not completed/cancelled --}}
                     @if(!in_array($order->status, ['completed', 'cancelled']))
                        <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                             @csrf
                             @method('PUT') {{-- Or PATCH --}}
                            <div class="input-group mb-2">
                                <select name="status" class="form-select">
                                     @foreach($statuses as $key => $label)
                                        <option value="{{ $key }}" {{ $key == $order->status ? 'selected' : '' }}>{{ $label }}</option>
                                     @endforeach
                                </select>
                                 <button type="submit" class="btn btn-primary">Update</button>
                             </div>
                             <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="notify_customer" value="1" id="notify_customer">
                                <label class="form-check-label" for="notify_customer">
                                    Notify Customer
                                </label>
                            </div>
                             @error('status') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </form>
                    @else
                         <p><small>Status cannot be changed for completed or cancelled orders.</small></p>
                    @endif
                 </div>
            </div>

            {{-- Payment Details --}}
             <div class="card mb-4">
                 <div class="card-header"><h3 class="card-title mb-0">Payment</h3></div>
                 <div class="card-body">
                    <p><strong>Method:</strong> {{ $order->payment_method ?? 'N/A' }} </p>
                    @if($order->payments->isNotEmpty())
                        @foreach($order->payments as $payment)
                            <p><strong>Status:</strong> <span class="fw-bold payment-status-{{ $payment->status }}">{{ $payment::statuses()[$payment->status] ?? ucfirst($payment->status) }}</span></p>
                            <p><strong>Amount Paid:</strong> AED {{ number_format($payment->amount, 2) }}</p>
                            @if($payment->transaction_id)
                             <p><small><strong>Transaction ID:</strong> {{ $payment->transaction_id }}</small></p>
                            @endif
                             <p><small><strong>Date:</strong> {{ $payment->created_at->format('d M Y, H:i') }}</small></p>
                             {{-- Link to Invoice --}}
                             @if($payment->invoice)
                                <p><small><strong>Invoice:</strong> <a href="#">#{{ $payment->invoice->invoice_number }}</a></small></p>
                             @endif
                             <hr>
                        @endforeach
                    @else
                        <p class="text-muted">No payment record found.</p>
                         {{-- Add button to manually record payment? --}}
                    @endif
                 </div>
            </div>

            {{-- Delivery Details --}}
             <div class="card mb-4">
                 <div class="card-header"><h3 class="card-title mb-0">Delivery</h3></div>
                 <div class="card-body">
                    @if($order->delivery)
                        @php $delivery = $order->delivery; @endphp
                         <p><strong>Assigned To:</strong> {{ $delivery->deliveryPersonnel->name ?? 'N/A' }}</p>
                         <p><strong>Status:</strong> <span class="fw-bold delivery-status-{{ $delivery->status }}">{{ ucfirst($delivery->status) }}</span></p>
                         <p><strong>Tracking #:</strong> {{ $delivery->tracking_number ?? 'N/A' }}</p>
                         <p><strong>Delivery Date:</strong> {{ $delivery->delivery_date ?? 'N/A' }}</p>
                         @if($delivery->confirmation_image)
                            <p><strong>Confirmation:</strong> <a href="{{ Storage::disk('public')->url($delivery->confirmation_image) }}" target="_blank">View Image</a></p>
                         @endif
                         {{-- Add Edit Delivery button? --}}
                    @else
                        <p class="text-muted">No delivery information assigned yet.</p>
                         {{-- Add Assign Delivery button? --}}
                    @endif
                 </div>
            </div>

        </div> {{-- End Right Column --}}
    </div> {{-- End Row --}}
@endsection