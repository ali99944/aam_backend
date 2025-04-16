{{-- Simplified Sidebar for Company --}}
<aside class="admin-sidebar" id="adminSidebar"> {{-- Reuse class --}}
    <div class="sidebar-header">
        <a href="{{ route('delivery-company.dashboard') }}" class="sidebar-logo-link">
            {{-- Show Company Logo if available --}}
            {{-- @if(auth('company')->user()->logo_url && auth('company')->user()->logo_url !== asset('images/placeholder-logo.png')) --}}
                 {{-- <img src="{{ auth('company')->user()->logo_url }}" alt="Logo" class="logo-icon" style="border-radius: 3px;"> --}}
            {{-- @else --}}
                 <x-lucide-building class="logo-icon"/> {{-- Default icon --}}
            {{-- @endif --}}
            {{-- <span class="logo-text">{{ auth('company')->user()->name }}</span> --}}
        </a>
    </div>

    <nav class="sidebar-nav">
        <ul>
            <li class="{{ request()->routeIs('company.dashboard') ? 'active' : '' }}">
                <a href="{{ route('delivery-company.dashboard') }}">
                    <x-lucide-layout-dashboard class="nav-icon" />
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
             {{-- <li class="{{ request()->routeIs('company.deliveries.*') ? 'active' : '' }}">
                <a href="{{ route('company.deliveries.index') }}">
                    <x-lucide-truck class="nav-icon" />
                    <span class="nav-text">Assigned Deliveries</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('company.personnel.*') ? 'active' : '' }}">
                <a href="{{ route('company.personnel.index') }}">
                    <x-lucide-users class="nav-icon" />
                    <span class="nav-text">Delivery Personnel</span>
                </a>
            </li> --}}
            {{-- Add Profile/Settings link later if needed --}}
            {{--
            <li class="{{ request()->routeIs('company.profile') ? 'active' : '' }}">
                <a href="#">
                    <x-lucide-settings class="nav-icon" />
                    <span class="nav-text">Settings</span>
                </a>
            </li>
            --}}
        </ul>
    </nav>

    <div class="sidebar-footer">
         <form method="POST" action="{{ route('delivery-company.dashboard') }}" id="company-logout-form">
              @csrf
              <a href="{{ route('delivery-company.dashboard') }}"
                 onclick="event.preventDefault(); document.getElementById('company-logout-form').submit();"
                 class="logout-link">
                    <x-lucide-log-out class="nav-icon" />
                    <span class="nav-text">Sign Out</span>
               </a>
         </form>
    </div>
</aside>