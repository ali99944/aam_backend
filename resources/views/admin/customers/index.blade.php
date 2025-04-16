@extends('layouts.admin')
@section('title', 'Manage Customers')

@push('styles')
<style>
.status-badge { font-size: 0.8em; padding: 3px 8px; border-radius: 5px; font-weight: 500;}
.status-badge-active { background-color: #d1e7dd; color: #0f5132; }
.status-badge-banned { background-color: #f8d7da; color: #842029; }
.status-badge-verification-required { background-color: #fff3cd; color: #664d03; border: 1px solid #ffeeba;}
.verification-icon { vertical-align: middle; }
.ban-info { font-size: 0.85em; color: #6c757d; }
.modal-body .form-label { font-weight: 500; }
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>Manage Customers</h1>
        {{-- Optional: Add "Add Customer" button if admin can create customers --}}
        {{-- <div class="actions">
            <a href="#" class="btn btn-primary">
                <x-lucide-plus class="icon-sm mr-2"/> Add New Customer
            </a>
        </div> --}}
    </div>

    {{-- Filter Form --}}
    <div class="card mb-4">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.customers.index') }}" class="form-inline flex-wrap">
                 {{-- Search --}}
                 <div class="form-group mr-2 mb-2">
                     <label for="search" class="mr-1 d-none d-sm-inline">Search:</label>
                    <input type="text" id="search" name="search" class="form-control form-control-sm" placeholder="Name or Email..." value="{{ request('search') }}">
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
                 {{-- Banned Status --}}
                 <div class="form-group mr-2 mb-2">
                    <label for="filter_banned" class="mr-1">Banned:</label>
                    <select id="filter_banned" name="banned" class="form-control form-control-sm">
                         <option value="all">All</option>
                         <option value="1" {{ request('banned') === '1' ? 'selected' : '' }}>Yes</option>
                         <option value="0" {{ request('banned') === '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                 {{-- Verified Status --}}
                 <div class="form-group mr-2 mb-2">
                    <label for="filter_verified" class="mr-1">Verified:</label>
                    <select id="filter_verified" name="verified" class="form-control form-control-sm">
                         <option value="all">All</option>
                         <option value="1" {{ request('verified') === '1' ? 'selected' : '' }}>Yes</option>
                         <option value="0" {{ request('verified') === '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                {{-- Submit --}}
                <div class="form-group mb-2">
                    <button type="submit" class="btn btn-secondary btn-sm">
                        <x-lucide-filter class="icon-sm mr-1"/> Filter
                    </button>
                     @if(request()->hasAny(['search', 'status', 'banned', 'verified']))
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-link btn-sm ml-1">Clear Filters</a>
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
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Verified</th>
                            <th>Banned</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $customer)
                            <tr>
                                <td><strong>{{ $customer->name }}</strong></td>
                                <td>{{ $customer->email }}</td>
                                <td>
                                    <span class="status-badge status-badge-{{ str_replace('_','-',$customer->status) }}">
                                        {{ $statuses[$customer->status] ?? ucfirst($customer->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($customer->is_email_verified)
                                        <x-lucide-check-circle class="text-success verification-icon" title="Verified"/>
                                    @else
                                        <x-lucide-x-circle class="text-warning verification-icon" title="Not Verified"/>
                                    @endif
                                </td>
                                <td>
                                    @if($customer->is_banned)
                                        <span class="text-danger">Yes</span>
                                        <small class="d-block ban-info" title="{{ $customer->ban_reason }}">
                                            On: {{ $customer->banned_at?->format('d M Y') }}
                                            @if($customer->ban_reason) | Reason: {{ Str::limit($customer->ban_reason, 30) }} @endif
                                        </small>
                                    @else
                                        <span class="text-muted">No</span>
                                    @endif
                                </td>
                                <td>{{ $customer->created_at->format('d M Y') }}</td>
                                <td class="actions">
                                    {{-- Ban / Unban Buttons --}}
                                    @if(!$customer->is_banned)
                                        <button type="button" class="btn btn-sm btn-outline-warning ban-btn"
                                                data-bs-toggle="modal" data-bs-target="#banModal"
                                                data-customer-id="{{ $customer->id }}"
                                                data-customer-name="{{ $customer->name }}"
                                                title="Ban Customer">
                                            <x-lucide-user-x />
                                        </button>
                                    @else
                                         <form action="{{ route('admin.customers.unban', $customer->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to unban {{ $customer->name }}?');">
                                            @csrf
                                            {{-- Use PATCH/PUT if preferred, requires method spoofing --}}
                                            {{-- @method('PATCH') --}}
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Unban Customer">
                                                <x-lucide-user-check />
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Delete Button --}}
                                    <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to DELETE customer {{ $customer->name }}? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Customer">
                                            <x-lucide-trash-2 />
                                        </button>
                                    </form>

                                     {{-- Optional View Button --}}
                                    {{-- <a href="#" class="btn btn-sm btn-outline-info" title="View Details"><x-lucide-eye /></a> --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No customers found matching your criteria.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
         @if ($customers->hasPages())
            <div class="card-footer">
                 {{ $customers->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    {{-- Ban Reason Modal --}}
    <div class="modal fade" id="banModal" tabindex="-1" aria-labelledby="banModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="banForm" method="POST" action=""> {{-- Action will be set by JS --}}
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="banModalLabel">Ban Customer: <span id="banCustomerName"></span></h5>
                         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                             <label for="ban_reason" class="form-label">Reason for Banning <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="ban_reason" name="ban_reason" rows="4" required></textarea>
                            <small class="text-muted">This reason may be displayed to the customer or used for internal records.</small>
                         </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Confirm Ban</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
{{-- If using Bootstrap 5 JS for modal --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script> {{-- Or include locally --}}

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const banModal = document.getElementById('banModal');
        if (banModal) {
            const banForm = document.getElementById('banForm');
            const banCustomerNameSpan = document.getElementById('banCustomerName');
            const banReasonTextarea = document.getElementById('ban_reason');

            banModal.addEventListener('show.bs.modal', function (event) {
                // Button that triggered the modal
                const button = event.relatedTarget;
                // Extract info from data-* attributes
                const customerId = button.getAttribute('data-customer-id');
                const customerName = button.getAttribute('data-customer-name');

                // Update the modal's content.
                banCustomerNameSpan.textContent = customerName;
                // Construct the form action URL
                const actionUrl = `/admin/customers/${customerId}/ban`; // Adjust URL based on your setup
                banForm.setAttribute('action', actionUrl);
                banReasonTextarea.value = ''; // Clear previous reason
            });
        }
    });
</script>
@endpush