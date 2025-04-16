{{-- Simplified Navbar for Company --}}
<header class="admin-navbar"> {{-- Reuse class --}}
    <div class="navbar-left">
        {{-- Toggle Button if using collapsible sidebar --}}
        <button class="sidebar-toggle-btn" id="sidebarToggleBtn" title="Toggle Sidebar">
             <x-lucide-panel-left-close class="icon-collapsed" />
             <x-lucide-panel-left-open class="icon-expanded" />
        </button>
         <div class="navbar-breadcrumbs">
            {{-- Simple Title --}}
            Company Portal
        </div>
    </div>

    <div class="navbar-right">
         {{-- Company Name/User Menu --}}
        <div class="dropdown user-menu">
            <button class="dropdown-toggle btn btn-link">
                 {{-- @if(auth('company')->user()->logo_url && auth('company')->user()->logo_url !== asset('images/placeholder-logo.png')) --}}
                    {{-- <img src="{{ auth('company')->user()->logo_url }}" alt="Logo" class="user-avatar" style="border-radius: 3px;"> --}}
                 {{-- @else --}}
                     <x-lucide-user class="user-avatar" style="padding: 5px; background: #eee; border-radius: 50%;"/>
                 {{-- @endif --}}
                {{-- <span class="user-name">{{ auth('company')->user()->name }}</span> --}}
                <x-lucide-chevron-down class="icon-xs" />
            </button>
            <div class="dropdown-menu dropdown-menu-right">
               {{-- <a href="#" class="dropdown-item"><x-lucide-settings-2 class="icon-sm" /> Settings</a>
               <div class="dropdown-divider"></div> --}}
                 <a href="{{ route('delivery-company.dashboard') }}"
                    class="dropdown-item"
                    onclick="event.preventDefault(); document.getElementById('company-logout-form').submit();">
                       <x-lucide-log-out class="icon-sm" /> Sign Out
                 </a>
            </div>
        </div>
    </div>
</header>