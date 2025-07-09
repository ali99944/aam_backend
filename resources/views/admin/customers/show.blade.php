@extends('layouts.admin')
@section('title', "تفاصيل العميل: {$customer->name}")

{{-- Push styles are at the top of this response --}}
@push('styles')
<style>
/* Styles from index view (status badges) */
.status-badge { font-size: 0.8em; padding: 3px 8px; border-radius: 5px; font-weight: 500;}
.status-badge-active { background-color: #d1e7dd; color: #0f5132; }
.status-badge-banned { background-color: #f8d7da; color: #842029; }
.status-badge-verification-required { background-color: #fff3cd; color: #664d03; border: 1px solid #ffeeba;}
/* Styles from order index (for tab content if using same badges) */
.status-badge-pending { background-color: #fff3cd; color: #664d03; border: 1px solid #ffeeba;}
.status-badge-processing { background-color: #cff4fc; color: #055160; border: 1px solid #bee5eb;}
.status-badge-in-check { background-color: #e2e3e5; color: #41464b; border: 1px solid #d3d6d8;}
.status-badge-completed { background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc;}
.status-badge-cancelled { background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7;}

/* Show page specific */
.customer-summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); /* Responsive columns */
    gap: 1.5rem; /* Spacing between cards */
    margin-bottom: 1.5rem;
}
.customer-summary-grid .card { margin-bottom: 0; } /* Remove default card margin if inside grid */

.info-item { display: flex; align-items: center; margin-bottom: 0.5rem; }
.info-item .lucide {
    width: 18px; /* Reduced icon size */
    height: 18px; /* Reduced icon size */
    margin-right: 0.5rem; /* LTR */
    color: #6c757d; /* Muted color */
}
html[dir="rtl"] .info-item .lucide { margin-right: 0; margin-left: 0.5rem; }

.stat-card-mini { border: 1px solid #eee; border-radius: 5px; padding: 15px; text-align: center; background-color: #f8f9fa; height: 100%; }
.stat-card-mini h6 { margin-bottom: 8px; color: #6c757d; font-size: 0.9em; text-transform: uppercase; font-weight: 500; }
.stat-card-mini p { margin-bottom: 0; font-size: 1.5em; font-weight: 600; color: #343a40; }

/* Plain JS Tabs CSS */
.tabs-container { margin-top: 1.5rem; }
.tab-nav {
    display: flex;
    border-bottom: 1px solid var(--border-color, #dee2e6);
    margin-bottom: 0; /* Remove default ul margin */
    padding-left: 0; /* Remove default ul padding */
    list-style: none;
}
.tab-nav-item { margin-bottom: -1px; /* To make active tab border connect */ }
.tab-nav-link {
    display: block;
    padding: 0.75rem 1.25rem;
    text-decoration: none;
    color: var(--text-muted-color, #6c757d);
    border: 1px solid transparent;
    border-top-left-radius: .25rem;
    border-top-right-radius: .25rem;
    background-color: #e9ecef;
    cursor: pointer;
    transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out;
    font-weight: 500;
}
.tab-nav-link:hover {
    color: var(--dark-color, #2c3e50);
    border-color: var(--border-color, #dee2e6);
}
.tab-nav-link.active {
    color: var(--primary-color, #0d6efd);
    background-color: #fff;
    border-color: var(--border-color, #dee2e6) var(--border-color, #dee2e6) #fff;
}
.tab-nav-link .lucide { margin-right: 0.3em; /* LTR */ vertical-align: text-bottom;}
html[dir="rtl"] .tab-nav-link .lucide { margin-right: 0; margin-left: 0.3em; }

.tab-content-container {
    border: 1px solid var(--border-color, #dee2e6);
    border-top: none;
    padding: 1.5rem;
    background-color: #fff;
    border-bottom-left-radius: .25rem;
    border-bottom-right-radius: .25rem;
}
.tab-panel { display: none; /* Hidden by default */ }
.tab-panel.active { display: block; }

.table th { white-space: nowrap; }
.actions.ws-nowrap .btn { margin: 2px; }
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>تفاصيل العميل: <span class="text-primary">{{ $customer->name }}</span></h1>
         <div class="actions">
             <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
                 <x-lucide-arrow-right class="icon-sm ms-1"/> العودة إلى قائمة العملاء
             </a>
             {{-- Optional: Add Edit button --}}
             {{-- <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-primary"><x-lucide-pencil class="icon-sm ms-1"/> تعديل العميل</a> --}}
        </div>
    </div>

    {{-- Customer Summary Grid --}}
    <div class="customer-summary-grid">
        {{-- Basic Info Card --}}
        <div class="card"> {{-- Removed h-100 to allow natural height --}}
             <div class="card-header"><h5 class="card-title mb-0">معلومات الاتصال</h5></div>
             <div class="card-body">
                <div class="info-item">
                    <x-lucide-mail style="width: 20px; height: 20px;" /> <span>{{ $customer->email }}</span>
                </div>
                <div class="info-item">
                    <x-lucide-phone style="width: 20px; height: 20px;"/> <span>{{ $customer->phone }}</span>
                </div>
                <hr class="my-2">
                 <div class="info-item">
                    <strong>الحالة:</strong>
                     <span class="status-badge status-badge-{{ str_replace('_','-',$customer->status) }} ms-2">
                        {{ \App\Models\Customer::statuses()[$customer->status] ?? ucfirst($customer->status) }}
                    </span>
                 </div>
                @if($customer->is_banned)
                     <div class="info-item text-danger mt-1">
                        <x-lucide-shield-ban />
                        <span>محظور بتاريخ {{ $customer->banned_at?->locale('ar')->translatedFormat('d M Y') }}</span>
                     </div>
                     <small class="d-block text-muted ps-4"><strong>السبب:</strong> {{ $customer->ban_reason ?: 'غير متوفر' }}</small>
                @endif
             </div>
         </div>

         {{-- Balance & Statistics Cards in a sub-grid or flex container --}}
         <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;"> {{-- Sub-grid for balance and stats --}}
            {{-- Balance Card --}}
            <div class="card"> {{-- Removed h-100 --}}
                 <div class="card-header"><h5 class="card-title mb-0">الرصيد</h5></div>
                 <div class="card-body text-center d-flex flex-column justify-content-center">
                    <p class="fs-3 fw-bold mb-0">دينار {{ number_format($customer->balance, 2) }}</p>
                 </div>
             </div>
            {{-- Statistics Card --}}
            <div class="card"> {{-- Removed h-100 --}}
                 <div class="card-header"><h5 class="card-title mb-0">الإحصائيات</h5></div>
                 <div class="card-body">
                     <div class="row g-3"> {{-- Increased gap --}}
                         <div class="col-md-4 col-6"> {{-- Responsive cols --}}
                             <div class="stat-card-mini">
                                 <h6>إجمالي الطلبات</h6>
                                 <p>{{ $statistics['total_orders'] }}</p>
                             </div>
                         </div>
                          <div class="col-md-4 col-6">
                             <div class="stat-card-mini">
                                 <h6>إجمالي المنفق</h6>
                                 <p>دينار {{ number_format($statistics['total_spent'], 0) }}</p>
                             </div>
                         </div>
                          <div class="col-md-4 col-12"> {{-- Full width on small --}}
                             <div class="stat-card-mini">
                                 <h6>متوسط الطلب</h6>
                                 <p>دينار {{ number_format($statistics['average_order_value'], 0) }}</p>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
    </div>

    {{-- Tabs for Orders, Payments, Invoices --}}
    <div class="tabs-container">
        {{-- Tab Navigation --}}
        <ul class="tab-nav" id="customerPlainTabs" role="tablist">
            <li class="tab-nav-item" role="presentation">
                <button class="tab-nav-link active" id="plain-orders-tab" data-tab-target="#plain-orders-panel" type="button" role="tab" aria-controls="plain-orders-panel" aria-selected="true">
                    <x-lucide-shopping-cart class="icon-sm"/> الطلبات ({{ $orders->total() }})
                </button>
            </li>
            <li class="tab-nav-item" role="presentation">
                <button class="tab-nav-link" id="plain-payments-tab" data-tab-target="#plain-payments-panel" type="button" role="tab" aria-controls="plain-payments-panel" aria-selected="false">
                     <x-lucide-credit-card class="icon-sm"/> المدفوعات ({{ $payments->total() }})
                </button>
            </li>
            <li class="tab-nav-item" role="presentation">
                <button class="tab-nav-link" id="plain-invoices-tab" data-tab-target="#plain-invoices-panel" type="button" role="tab" aria-controls="plain-invoices-panel" aria-selected="false">
                    <x-lucide-file-text class="icon-sm"/> الفواتير ({{ $invoices->total() }})
                </button>
            </li>
        </ul>

        {{-- Tab Content --}}
        <div class="tab-content-container" id="customerPlainTabContent">
            <div class="tab-panel active" id="plain-orders-panel" role="tabpanel" aria-labelledby="plain-orders-tab">
                 @include('admin.customers._tab_orders', ['orders' => $orders])
            </div>
            <div class="tab-panel" id="plain-payments-panel" role="tabpanel" aria-labelledby="plain-payments-tab">
                 @include('admin.customers._tab_payments', ['payments' => $payments])
            </div>
            <div class="tab-panel" id="plain-invoices-panel" role="tabpanel" aria-labelledby="plain-invoices-tab">
                 @include('admin.customers._tab_invoices', ['invoices' => $invoices])
            </div>
        </div>
    </div>
@endsection

 @push('scripts')
    {{-- REMOVED Bootstrap JS for Tabs --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script> --}}

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabLinks = document.querySelectorAll('.tab-nav-link');
            const tabPanels = document.querySelectorAll('.tab-panel');

            if (tabLinks.length > 0 && tabPanels.length > 0) {
                tabLinks.forEach(link => {
                    link.addEventListener('click', function (event) {
                        event.preventDefault();

                        // Deactivate all links and panels
                        tabLinks.forEach(l => l.classList.remove('active'));
                        tabPanels.forEach(p => p.classList.remove('active'));

                        // Activate clicked link
                        this.classList.add('active');
                        this.setAttribute('aria-selected', 'true');


                        // Activate corresponding panel
                        const targetPanelId = this.getAttribute('data-tab-target');
                        if (targetPanelId) {
                            const targetPanel = document.querySelector(targetPanelId);
                            if (targetPanel) {
                                targetPanel.classList.add('active');
                            }
                        }
                    });
                });

                // Optional: Activate the first tab by default if no 'active' class is set in HTML
                if (document.querySelector('.tab-nav-link.active') === null) {
                    tabLinks[0].click();
                }
            }
        });
    </script>
@endpush