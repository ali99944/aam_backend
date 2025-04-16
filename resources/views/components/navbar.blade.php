<header class="admin-navbar">
    <div class="navbar-left">
        {{-- Sidebar Toggle Button --}}

        <button class="sidebar-toggle-btn" id="sidebarToggleBtn" title="Toggle Sidebar">
            <x-lucide-panel-left-close class="icon-collapsed" /> {{-- Icon shown when sidebar is collapsed --}}
            <x-lucide-panel-left-open class="icon-expanded" /> {{-- Icon shown when sidebar is expanded --}}
        </button>

        {{-- Breadcrumbs or Page Title (Optional) --}}
        <div class="navbar-breadcrumbs">
           {{-- You can yield a breadcrumb section here or dynamically generate it --}}
           {{-- Example: Dashboard / Products / Edit --}}
        </div>
    </div>

    <div class="navbar-right">
         {{-- Language Switcher (Example) --}}
         {{-- You'll need backend logic to handle language switching --}}
         {{--
         <div class="dropdown lang-switcher">
            <button class="dropdown-toggle btn btn-sm btn-outline-secondary">
                <x-lucide-languages class="icon-sm" /> {{ strtoupper(app()->getLocale()) }}
            </button>
            <div class="dropdown-menu">
                <a href="{{ route('setLocale', 'en') }}" class="dropdown-item">English</a>
                <a href="{{ route('setLocale', 'ar') }}" class="dropdown-item">العربية</a>
            </div>
         </div>
          --}}

        {{-- User Menu Dropdown --}}
        <div class="dropdown user-menu">
            <button class="dropdown-toggle btn btn-link">
                <img src="{{ asset('images/default-avatar.png') }}" alt="User Avatar" class="user-avatar">
                <span class="user-name">{{ Auth::user()->name ?? 'Admin' }}</span> {{-- Assumes user is logged in --}}
                <x-lucide-chevron-down class="icon-xs" />
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="#" class="dropdown-item"> {{-- Link to profile page --}}
                    <x-lucide-user class="icon-sm" /> Profile
                </a>
                <a href="#" class="dropdown-item"> {{-- Link to settings --}}
                    <x-lucide-settings-2 class="icon-sm" /> Settings
                </a>
                <div class="dropdown-divider"></div>
                 {{-- Logout Form Trigger --}}
                 <a href="#"
                    class="dropdown-item"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                       <x-lucide-log-out class="icon-sm" /> Sign Out
                 </a>
            </div>
        </div>
    </div>
</header>