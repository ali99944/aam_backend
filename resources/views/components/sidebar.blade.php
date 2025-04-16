{{-- Use `session` or a dedicated user preference service to get sidebar state --}}
@php
    $isCollapsed = session('sidebar_collapsed', false); // Default to not collapsed
@endphp

<aside class="admin-sidebar {{ $isCollapsed ? 'collapsed' : '' }}" id="adminSidebar">
    <div class="sidebar-header">
        {{-- Replace with your actual logo --}}
        <a href="{{ route('admin.dashboard') }}" class="sidebar-logo-link">
            <img src="https://img.freepik.com/free-vector/supermarket-logo-concept_23-2148467758.jpg?ga=GA1.1.1587902589.1744381918&semt=ais_hybrid&w=740" alt="Logo Icon" class="logo-icon">
            <span class="logo-text">{{ config('app.name', 'AAM Store') }}</span>
        </a>
    </div>

    <nav class="sidebar-nav">
        <ul>
            {{-- Dashboard --}}
            <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}">
                    <x-lucide-layout-dashboard class="nav-icon" />
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            {{-- Catalog Section --}}
            <li class="nav-section-title"><span class="nav-text">Catalog</span></li>
            <li class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <a href="{{ route('admin.products.index') }}"> {{-- Ensure this route exists --}}
                    <x-lucide-package class="nav-icon" />
                    <span class="nav-text">Products</span>
                </a>
            </li>


            <li class="{{ request()->routeIs('admin.admin.categories.*') ? 'active' : '' }}"> {{-- Added active state check --}}
                <a href="{{ route('admin.categories.index') }}"> {{-- Corrected route --}}
                    <x-lucide-folder-tree class="nav-icon" />
                    <span class="nav-text">Categories</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.subcategories.*') ? 'active' : '' }}">
                <a href="{{ route('admin.subcategories.index') }}">
                    <x-lucide-folder-symlink class="nav-icon" /> {{-- Example icon --}}
                    <span class="nav-text">Sub Categories</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                <a href="{{ route('admin.brands.index') }}">
                    <x-lucide-tag class="nav-icon" /> {{-- Example icon --}}
                    <span class="nav-text">Brands</span>
                </a>
            </li>

            <li class="nav-section-title"><span class="nav-text">Sales & Logistics</span></li> {{-- Renamed Section? --}}
            {{-- ... Orders, Subscriptions ... --}}
             <li class="{{ request()->routeIs('admin.deliveries.*') ? 'active' : '' }}">
                <a href="#"> {{-- Replace with route('admin.deliveries.index') later --}}
                    <x-lucide-truck class="nav-icon" />
                    <span class="nav-text">Deliveries</span>
                </a>
            </li>
            {{-- Add Delivery Company Link --}}
            <li class="{{ request()->routeIs('admin.delivery-companies.*') ? 'active' : '' }}">
                <a href="{{ route('admin.delivery-companies.index') }}">
                    <x-lucide-building class="nav-icon" /> {{-- Example icon --}}
                    <span class="nav-text">Delivery Companies</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.delivery-personnel.*') ? 'active' : '' }}">
                <a href="{{ route('admin.delivery-personnel.index') }}">
                    {{-- <x-lucide-moped class="nav-icon" /> --}}
                    <x-lucide-bike class="nav-icon" /> {{-- Or another icon --}}
                    <span class="nav-text">Delivery Personnel</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.delivery-fees.*') ? 'active' : '' }}">
                <a href="{{ route('admin.delivery-fees.index') }}">
                    <x-lucide-map-pin class="nav-icon" /> {{-- Or wallet/coins --}}
                    <span class="nav-text">City Delivery Fees</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <a href="{{ route('admin.orders.index') }}">
                    <x-lucide-shopping-cart class="nav-icon" />
                    <span class="nav-text">Orders</span>
                </a>
            </li>



             {{-- Customers & Staff --}}
            <li class="nav-section-title"><span class="nav-text">Users</span></li>
            <li class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                <a href="{{ route('admin.customers.index') }}"> {{-- Corrected route --}}
                    <x-lucide-users class="nav-icon" />
                    <span class="nav-text">Customers</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('staff.*') ? 'active' : '' }}"> {{-- Admins/Employees --}}
                <a href="#"> {{-- Replace with route('staff.index') later --}}
                    <x-lucide-user-cog class="nav-icon" />
                    <span class="nav-text">Staff & Roles</span>
                </a>
            </li>

            <li class="nav-section-title"><span class="nav-text">Content & Support</span></li>
            <li class="{{ request()->routeIs('admin.support-tickets.*') ? 'active' : '' }}">
                <a href="{{ route('admin.support-tickets.index') }}">
                    <x-lucide-life-buoy class="nav-icon" />
                    <span class="nav-text">Support Tickets</span>
                     @inject('ticketCounter', 'App\Models\SupportTicket')
                    @if($pendingCount = $ticketCounter::whereIn('status',['open','customer_reply'])->count())
                        <span class="badge bg-info rounded-pill ms-auto">{{ $pendingCount }}</span> {{-- Pending count --}}
                    @endif
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.faq-categories.*') || request()->routeIs('admin.faq-categories.*') ? 'active' : '' }}"> {{-- Combined active state --}}
                <a href="{{ route('admin.faq-categories.index') }}">
                    <x-lucide-help-circle class="nav-icon" />
                    <span class="nav-text">FAQs Categories</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.faqs.*') || request()->routeIs('admin.faq-categories.*') ? 'active' : '' }}"> {{-- Combined active state --}}
                <a href="{{ route('admin.faqs.index') }}">
                    <x-lucide-help-circle class="nav-icon" />
                    <span class="nav-text">FAQs</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.payment-methods.*') ? 'active' : '' }}">
                <a href="{{ route('admin.payment-methods.index') }}">
                    <x-lucide-credit-card class="nav-icon" />
                    <span class="nav-text">Payment Methods</span>
                </a>
            </li>

            <li class="nav-section-title"><span class="nav-text">Locations</span></li>
            <li class="{{ request()->routeIs('admin.locations.countries.*') ? 'active' : '' }}">
                <a href="{{ route('admin.countries.index') }}">
                    <x-lucide-flag class="nav-icon" />
                    <span class="nav-text">Countries</span>
                </a>
            </li>
             <li class="{{ request()->routeIs('admin.locations.states.*') ? 'active' : '' }}">
                <a href="{{ route('admin.states.index') }}">
                    <x-lucide-map-pin class="nav-icon" />
                    <span class="nav-text">States / Regions</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.locations.cities.*') ? 'active' : '' }}">
                <a href="{{ route('admin.cities.index') }}">
                    <x-lucide-building-2 class="nav-icon" />
                    <span class="nav-text">Cities</span>
                </a>
            </li>
             <li class="{{ request()->routeIs('admin.locations.timezones.*') ? 'active' : '' }}">
                <a href="{{ route('admin.timezones.index') }}">
                    <x-lucide-clock class="nav-icon" />
                    <span class="nav-text">Timezones</span>
                </a>
            </li>

            <li class="nav-section-title"><span class="nav-text">Marketing</span></li>
             <li class="{{ request()->routeIs('admin.offers.*') ? 'active' : '' }}">
                <a href="{{ route('admin.offers.index') }}">
                    <x-lucide-megaphone class="nav-icon" /> {{-- Or image, tag --}}
                    <span class="nav-text">Offers / Banners</span>
                </a>
            </li>
             <li class="{{ request()->routeIs('admin.discounts.*') ? 'active' : '' }}">
                <a href="{{ route('admin.discounts.index') }}">
                    <x-lucide-percent-circle class="nav-icon" />
                    <span class="nav-text">Discount Codes</span>
                </a>
            </li>

            {{-- Finance & Vendors --}}
            <li class="nav-section-title"><span class="nav-text">Finance</span></li>
            <li class="{{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">
                <a href="{{ route('admin.suppliers.index') }}">
                    <x-lucide-contact class="nav-icon" /> {{-- Or truck, building --}}
                    <span class="nav-text">Suppliers</span> {{-- Changed from Vendors --}}

                </a>
            </li>
             {{-- Expenses --}}
             <li class="{{ request()->routeIs('admin.expenses.*') ? 'active' : '' }}">
                <a href="{{ route('admin.expenses.index') }}">
                    <x-lucide-receipt class="nav-icon" />
                    <span class="nav-text">Expenses</span>
                </a>
            </li>
             {{-- Expense Categories --}}
             <li class="{{ request()->routeIs('admin.expense-categories.*') ? 'active' : '' }}">
                <a href="{{ route('admin.expense-categories.index') }}">
                    <x-lucide-folder-open class="nav-icon" /> {{-- Or other icon --}}
                    <span class="nav-text">Expense Categories</span>
                </a>
            </li>
             <li class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <a href="#"> {{-- Replace with route('reports.index') later --}}
                    <x-lucide-bar-chart-3 class="nav-icon" />
                    <span class="nav-text">Reports</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                <a href="{{ route('admin.payments.index') }}">
                    <x-lucide-landmark class="nav-icon" /> {{-- Or wallet --}}
                    <span class="nav-text">Payment History</span>
                </a>
            </li>


            {{-- System Settings --}}
            <li class="nav-section-title"><span class="nav-text">System</span></li>
            <li class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <a href="{{ route('admin.settings.index') }}"> {{-- Updated Route --}}
                    <x-lucide-settings class="nav-icon" />
                    <span class="nav-text">Settings</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.languages.*') ? 'active' : '' }}">
                <a href="{{ route('admin.languages.index') }}">
                    <x-lucide-languages class="nav-icon" />
                    <span class="nav-text">Languages</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.seo.*') ? 'active' : '' }}">
                <a href="{{ route('admin.seo.index') }}">
                    <x-lucide-search-code class="nav-icon" /> {{-- Example icon --}}
                    <span class="nav-text">Page SEO</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.policies.*') ? 'active' : '' }}">
                <a href="{{ route('admin.policies.index') }}">
                    <x-lucide-shield-check class="nav-icon" /> {{-- Example icon --}}
                    <span class="nav-text">Policies</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.contact-messages.*') ? 'active' : '' }}">
                <a href="{{ route('admin.contact-messages.index') }}">
                    <x-lucide-mail class="nav-icon" />
                    <span class="nav-text">Contact Messages</span>
                    @inject('contactMessageCounter', 'App\Models\ContactMessage') {{-- Inject model for count --}}
                    @if($unreadCount = $contactMessageCounter::unread()->count())
                        <span class="badge bg-danger rounded-pill ms-auto">{{ $unreadCount }}</span> {{-- Unread count badge --}}
                    @endif
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.templates.*') ? 'active' : '' }}">
                <a href="{{ route('admin.templates.index') }}">
                    <x-lucide-mail-check class="nav-icon" /> {{-- Or palette --}}
                    <span class="nav-text">Email Templates</span>
                </a>
            </li>

        </ul>
    </nav>

    <div class="sidebar-footer">
         {{-- Logout Form --}}
         <form method="POST" action="#" id="logout-form">
              @csrf
              <a href="#"
                 onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                 class="logout-link">
                    <x-lucide-log-out class="nav-icon" />
                    <span class="nav-text">Sign Out</span>
               </a>
         </form>
    </div>
</aside>