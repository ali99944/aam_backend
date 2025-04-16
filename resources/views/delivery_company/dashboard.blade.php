{{-- resources/views/delivery_company/dashboard.blade.php --}}
@extends('layouts.delivery_company')
@section('title', 'Dashboard')

@section('content')
    <div class="content-header">
        <h1>Dashboard</h1>
    </div>

    {{-- Statistics Cards --}}
    <div class="stats-container mb-4">
         {{-- Adapt stat card styling/structure from admin --}}
        <div class="stat-card secondary"><div class="stat-icon"><x-lucide-users/></div><div class="stat-info"><h3>Total Personnel</h3><p>{{ $stats['totalPersonnel'] }}</p></div></div>
        <div class="stat-card success"><div class="stat-icon"><x-lucide-user-check/></div><div class="stat-info"><h3>Active Personnel</h3><p>{{ $stats['activePersonnel'] }}</p></div></div>
        <div class="stat-card primary"><div class="stat-icon"><x-lucide-truck/></div><div class="stat-info"><h3>Total Deliveries</h3><p>{{ $stats['totalDeliveries'] }}</p></div></div>
        <div class="stat-card warning"><div class="stat-icon"><x-lucide-loader/></div><div class="stat-info"><h3>Pending Deliveries</h3><p>{{ $stats['pendingDeliveries'] }}</p></div></div>
        <div class="stat-card info"><div class="stat-icon"><x-lucide-package-check/></div><div class="stat-info"><h3>Completed Today</h3><p>{{ $stats['completedToday'] }}</p></div></div>
    </div>

    <div class="row">
        {{-- Recent Deliveries --}}
        <div class="col-lg-7 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Assigned Deliveries</h3>
                     <a href="{{ route('delivery-company.dashboard') }}" class="btn btn-sm btn-outline-secondary float-end">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="admin-table mb-0"> {{-- Use admin table styles --}}
                            <thead><tr><th>Order #</th><th>Personnel</th><th>Status</th><th>Assigned</th></tr></thead>
                            <tbody>
                                @forelse($recentDeliveries as $delivery)
                                <tr>
                                    <td><a href="{{ route('delivery-company.dashboard') }}">{{ $delivery->order->track_code ?? $delivery->order->id }}</a></td>
                                    <td>{{ $delivery->deliveryPersonnel->name ?? 'N/A' }}</td>
                                    <td><span class="badge status-{{ $delivery->status ?? 'unknown' }}">{{ ucfirst($delivery->status ?? 'Unknown') }}</span></td>
                                    <td>{{ $delivery->created_at->diffForHumans() }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center py-3">No recent deliveries found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

         {{-- Recent Personnel --}}
         <div class="col-lg-5 mb-4">
             <div class="card">
                 <div class="card-header">
                    <h3 class="card-title">Recent Personnel</h3>
                    <a href="{{ route('delivery-company.dashboard') }}" class="btn btn-sm btn-outline-secondary float-end">View All</a>
                 </div>
                 <div class="card-body">
                     <ul class="list-group list-group-flush">
                        @forelse($recentPersonnel as $person)
                         <li class="list-group-item d-flex justify-content-between align-items-center">
                             <div>
                                 <img src="{{ $person->avatar_url }}" alt="" height="30" width="30" class="rounded-circle me-2">
                                 {{ $person->name }}
                             </div>
                             <span class="badge {{ $person->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $person->is_active ? 'Active' : 'Inactive' }}</span>
                         </li>
                        @empty
                         <li class="list-group-item text-center text-muted">No delivery personnel found.</li>
                        @endforelse
                     </ul>
                 </div>
             </div>
        </div>
    </div>

@endsection

 @push('styles') <style> /* Badge styles if needed */ </style> @endpush