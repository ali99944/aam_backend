@extends('layouts.admin')

@section('title', 'Admin Dashboard - AAM Store')

@push('styles')
    {{-- Add specific CSS for dashboard if needed --}}
@endpush

@section('content')
    <div class="content-header">
        <h1>Dashboard Overview</h1>
         {{-- Optional: Add date range filter or quick actions here --}}
    </div>

    {{-- Statistics Cards --}}
    {{-- Use data passed from your controller --}}
    <div class="stats-container">
        <div class="stat-card primary">
            <div class="stat-icon">
                <x-lucide-dollar-sign />
            </div>
            <div class="stat-info">
                <h3>Today's Sales</h3>
                <p>{{ $stats['today_sales'] ?? 'AED 0.00' }}</p> {{-- Format as currency --}}
            </div>
        </div>
        <div class="stat-card success">
             <div class="stat-icon">
                <x-lucide-shopping-cart />
            </div>
            <div class="stat-info">
                <h3>New Orders (24h)</h3>
                <p>{{ $stats['new_orders'] ?? 0 }}</p>
            </div>
        </div>
        <div class="stat-card warning">
             <div class="stat-icon">
                <x-lucide-truck />
            </div>
            <div class="stat-info">
                <h3>Pending Deliveries</h3>
                <p>{{ $stats['pending_deliveries'] ?? 0 }}</p>
             </div>
        </div>
        <div class="stat-card info">
             <div class="stat-icon">
                <x-lucide-users />
            </div>
             <div class="stat-info">
                <h3>New Customers (7d)</h3>
                <p>{{ $stats['new_customers'] ?? 0 }}</p>
             </div>
        </div>
         <div class="stat-card danger">
             <div class="stat-icon">
                <x-lucide-package-x />
            </div>
             <div class="stat-info">
                <h3>Low Stock Items</h3>
                <p>{{ $stats['low_stock'] ?? 0 }}</p>
             </div>
        </div>
         <div class="stat-card secondary">
             <div class="stat-icon">
                <x-lucide-wallet />
            </div>
             <div class="stat-info">
                <h3>Total Revenue (Month)</h3>
                <p>{{ $stats['month_revenue'] ?? 'AED 0.00' }}</p> {{-- Format as currency --}}
             </div>
        </div>

    </div>

    {{-- Other Dashboard Sections (e.g., Recent Orders Table, Charts) --}}
    <div class="dashboard-sections">
        <div class="dashboard-section">
            <h2>Recent Orders</h2>
            {{-- Placeholder for recent orders table --}}
            <div class="table-responsive"> {{-- Make tables scroll horizontally on small screens --}}
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Loop through $recentOrders from controller --}}
                        <tr>
                            <td>#12345</td>
                            <td>John Doe</td>
                            <td><span class="badge status-processing">Processing</span></td>
                            <td>AED 150.00</td>
                            <td>{{ now()->subHours(2)->format('Y-m-d H:i') }}</td>
                            <td><a href="#" class="btn btn-sm btn-outline-primary">View</a></td>
                        </tr>
                         <tr>
                            <td>#12344</td>
                            <td>Jane Smith</td>
                            <td><span class="badge status-shipped">Shipped</span></td>
                            <td>AED 85.50</td>
                            <td>{{ now()->subDay()->format('Y-m-d H:i') }}</td>
                            <td><a href="#" class="btn btn-sm btn-outline-primary">View</a></td>
                        </tr>
                        {{-- Add more rows or a message if no recent orders --}}
                    </tbody>
                </table>
            </div>
        </div>

        <div class="dashboard-section">
            <h2>Activity Feed</h2>
            {{-- Placeholder for activity feed --}}
            <ul class="activity-list">
                 <li>
                     <x-lucide-user-plus class="icon-sm text-success" />
                     <span>New customer 'Ahmed Ali' registered.</span>
                     <small class="text-muted">{{ now()->subMinutes(15)->diffForHumans() }}</small>
                 </li>
                 <li>
                     <x-lucide-package class="icon-sm text-info" />
                     <span>Product 'Laptop Stand' updated by Admin.</span>
                      <small class="text-muted">{{ now()->subHours(1)->diffForHumans() }}</small>
                 </li>
                 <li>
                    <x-lucide-alert-circle class="icon-sm text-warning" />
                     <span>Stock for 'Wireless Mouse' is low (3 remaining).</span>
                      <small class="text-muted">{{ now()->subHours(3)->diffForHumans() }}</small>
                 </li>
            </ul>
        </div>
    </div>

@endsection

@push('scripts')
    {{-- Add JS for charts if you implement them --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
    {{-- <script> /* Chart initialization logic */ </script> --}}
@endpush