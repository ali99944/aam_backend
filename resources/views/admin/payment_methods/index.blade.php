@extends('layouts.admin')
@section('title', 'Manage Payment Methods')

@push('styles')
<style>
.table-logo-preview { height: 30px; width: auto; max-width: 80px; object-fit: contain; vertical-align: middle; background: #f8f9fa; border: 1px solid #eee; padding: 2px;}
.status-icon { vertical-align: middle; margin-left: 4px; }
.status-enabled { color: var(--success-color); }
.status-disabled { color: var(--secondary-color); }
.status-yes { color: var(--success-color); }
.status-no { color: var(--secondary-color); }
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>Manage Payment Methods</h1>
        <div class="actions"><a href="{{ route('admin.payment-methods.create') }}" class="btn btn-primary"><x-lucide-plus class="icon-sm mr-2"/> Add New Method</a></div>
    </div>

    {{-- Add Filter/Search Form if needed --}}

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Provider</th>
                            <th class="text-center">Enabled</th>
                            <th class="text-center">Default</th>
                            <th class="text-center">Test Mode</th>
                            <th class="text-center">Online</th>
                            <th>Order</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($methods as $method)
                            <tr>
                                <td class="text-center"><img src="{{ $method->image_url }}" alt="{{ $method->name }}" class="table-logo-preview"></td>
                                <td><strong>{{ $method->name }}</strong></td>
                                <td><code>{{ $method->code }}</code></td>
                                <td>{{ $method->gateway_provider ?? '-' }}</td>
                                <td class="text-center">
                                    @if($method->is_enabled) <x-lucide-check-circle class="status-icon status-enabled" title="Yes"/>
                                    @else <x-lucide-x-circle class="status-icon status-disabled" title="No"/> @endif
                                </td>
                                <td class="text-center">
                                     @if($method->is_default) <x-lucide-star class="status-icon status-yes" fill="currentColor" title="Yes"/>
                                     @else <span class="text-muted">-</span> @endif
                                </td>
                                <td class="text-center">
                                    @if($method->is_test_mode) <x-lucide-flask-conical class="status-icon text-warning" title="Yes"/>
                                    @else <span class="text-muted">-</span> @endif
                                </td>
                                <td class="text-center">
                                     @if($method->is_online) <x-lucide-wifi class="status-icon status-yes" title="Yes"/>
                                     @else <x-lucide-wifi-off class="status-icon status-no" title="No"/> @endif
                                </td>
                                <td>{{ $method->display_order }}</td>
                                <td class="actions">
                                    <a href="{{ route('admin.payment-methods.edit', $method->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><x-lucide-pencil /></a>
                                    <form action="{{ route('admin.payment-methods.destroy', $method->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure? Deleting might affect past orders or checkout.');"> @csrf @method('DELETE') <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><x-lucide-trash-2 /></button></form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-center py-4">No payment methods configured yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($methods->hasPages())
            <div class="card-footer"> {{ $methods->links() }} </div>
        @endif
    </div>
@endsection