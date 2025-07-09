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
                    <span class="nav-text">لوحة التحكم</span>
                </a>
            </li>

            {{-- Catalog Section --}}
            <li class="nav-section-title"><span class="nav-text">الكتالوج</span></li>
            <li class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <a href="{{ route('admin.products.index') }}"> {{-- Ensure this route exists --}}
                    <x-lucide-package class="nav-icon" />
                    <span class="nav-text">المنتجات</span>
                </a>
            </li>


            <li class="{{ request()->routeIs('admin.admin.categories.*') ? 'active' : '' }}"> {{-- Added active state check --}}
                <a href="{{ route('admin.categories.index') }}"> {{-- Corrected route --}}
                    <x-lucide-folder-tree class="nav-icon" />
                    <span class="nav-text">الاقسام</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.subcategories.*') ? 'active' : '' }}">
                <a href="{{ route('admin.subcategories.index') }}">
                    <x-lucide-folder-symlink class="nav-icon" /> {{-- Example icon --}}
                    <span class="nav-text">الاقسام الفرعية</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                <a href="{{ route('admin.brands.index') }}">
                    <x-lucide-tag class="nav-icon" /> {{-- Example icon --}}
                    <span class="nav-text">العلامات التجارية</span>
                </a>
            </li>

            <li class="nav-section-title"><span class="nav-text">المبيعات و اللوجيستية</span></li> {{-- Renamed Section? --}}
            {{-- ... Orders, Subscriptions ... --}}
             <li class="{{ request()->routeIs('admin.deliveries.*') ? 'active' : '' }}">
                <a href="#"> {{-- Replace with route('admin.deliveries.index') later --}}
                    <x-lucide-truck class="nav-icon" />
                    <span class="nav-text">طلبات التوصيل</span>
                </a>
            </li>
            {{-- Add Delivery Company Link --}}
            <li class="{{ request()->routeIs('admin.delivery-companies.*') ? 'active' : '' }}">
                <a href="{{ route('admin.delivery-companies.index') }}">
                    <x-lucide-building class="nav-icon" /> {{-- Example icon --}}
                    <span class="nav-text">شركات التوصيل</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.delivery-personnel.*') ? 'active' : '' }}">
                <a href="{{ route('admin.delivery-personnel.index') }}">
                    {{-- <x-lucide-moped class="nav-icon" /> --}}
                    <x-lucide-bike class="nav-icon" /> {{-- Or another icon --}}
                    <span class="nav-text">عمال التوصيل</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.delivery-fees.*') ? 'active' : '' }}">
                <a href="{{ route('admin.delivery-fees.index') }}">
                    <x-lucide-map-pin class="nav-icon" /> {{-- Or wallet/coins --}}
                    <span class="nav-text">ضريبة التوصيل للمدن</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <a href="{{ route('admin.orders.index') }}">
                    <x-lucide-shopping-cart class="nav-icon" />
                    <span class="nav-text">الطلبات</span>
                </a>
            </li>



             {{-- Customers & Staff --}}
            <li class="nav-section-title"><span class="nav-text">المستخدمين</span></li>
            <li class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                <a href="{{ route('admin.customers.index') }}"> {{-- Corrected route --}}
                    <x-lucide-users class="nav-icon" />
                    <span class="nav-text">الزبائن</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('staff.*') ? 'active' : '' }}"> {{-- Admins/Employees --}}
                <a href="#"> {{-- Replace with route('staff.index') later --}}
                    <x-lucide-user-cog class="nav-icon" />
                    <span class="nav-text">الموظفين و الصلاحيات</span>
                </a>
            </li>

            <li class="nav-section-title"><span class="nav-text">المحتوي و الدعم</span></li>
            <li class="{{ request()->routeIs('admin.support-tickets.*') ? 'active' : '' }}">
                <a href="{{ route('admin.support-tickets.index') }}">
                    <x-lucide-life-buoy class="nav-icon" />
                    <span class="nav-text">تذاكر الدعم</span>
                     @inject('ticketCounter', 'App\Models\SupportTicket')
                    @if($pendingCount = $ticketCounter::whereIn('status',['open','customer_reply'])->count())
                        <span class="badge bg-info rounded-pill ms-auto">{{ $pendingCount }}</span> {{-- Pending count --}}
                    @endif
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.faq-categories.*') || request()->routeIs('admin.faq-categories.*') ? 'active' : '' }}"> {{-- Combined active state --}}
                <a href="{{ route('admin.faq-categories.index') }}">
                    <x-lucide-help-circle class="nav-icon" />
                    <span class="nav-text">اقسام الاسئلة الشائعة</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.faqs.*') || request()->routeIs('admin.faq-categories.*') ? 'active' : '' }}"> {{-- Combined active state --}}
                <a href="{{ route('admin.faqs.index') }}">
                    <x-lucide-help-circle class="nav-icon" />
                    <span class="nav-text">الاسئلة الشائعة</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.payment-methods.*') ? 'active' : '' }}">
                <a href="{{ route('admin.payment-methods.index') }}">
                    <x-lucide-credit-card class="nav-icon" />
                    <span class="nav-text">طرق الدفع</span>
                </a>
            </li>

            <li class="nav-section-title"><span class="nav-text">المواقع</span></li>
            <li class="{{ request()->routeIs('admin.locations.countries.*') ? 'active' : '' }}">
                <a href="{{ route('admin.countries.index') }}">
                    <x-lucide-flag class="nav-icon" />
                    <span class="nav-text">الدول</span>
                </a>
            </li>
             <li class="{{ request()->routeIs('admin.locations.states.*') ? 'active' : '' }}">
                <a href="{{ route('admin.states.index') }}">
                    <x-lucide-map-pin class="nav-icon" />
                    <span class="nav-text">الاحياء و المقاطعات</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.locations.cities.*') ? 'active' : '' }}">
                <a href="{{ route('admin.cities.index') }}">
                    <x-lucide-building-2 class="nav-icon" />
                    <span class="nav-text">المدن الداخلية</span>
                </a>
            </li>
             <li class="{{ request()->routeIs('admin.locations.timezones.*') ? 'active' : '' }}">
                <a href="{{ route('admin.timezones.index') }}">
                    <x-lucide-clock class="nav-icon" />
                    <span class="nav-text">المناطق الزمنية</span>
                </a>
            </li>

            <li class="nav-section-title"><span class="nav-text">Marketing</span></li>
             <li class="{{ request()->routeIs('admin.offers.*') ? 'active' : '' }}">
                <a href="{{ route('admin.offers.index') }}">
                    <x-lucide-megaphone class="nav-icon" /> {{-- Or image, tag --}}
                    <span class="nav-text">العروض و الترويج</span>
                </a>
            </li>
             <li class="{{ request()->routeIs('admin.discounts.*') ? 'active' : '' }}">
                <a href="{{ route('admin.discounts.index') }}">
                    <x-lucide-percent-circle class="nav-icon" />
                    <span class="nav-text">اكواد الخصومات</span>
                </a>
            </li>

            {{-- Finance & Vendors --}}
            <li class="nav-section-title"><span class="nav-text">الامور المالية</span></li>
            <li class="{{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">
                <a href="{{ route('admin.suppliers.index') }}">
                    <x-lucide-contact class="nav-icon" /> {{-- Or truck, building --}}
                    <span class="nav-text">مزودين المنتجات</span> {{-- Changed from Vendors --}}

                </a>
            </li>
             {{-- Expenses --}}
             <li class="{{ request()->routeIs('admin.expenses.*') ? 'active' : '' }}">
                <a href="{{ route('admin.expenses.index') }}">
                    <x-lucide-receipt class="nav-icon" />
                    <span class="nav-text">المصاريف</span>
                </a>
            </li>
             {{-- Expense Categories --}}
             <li class="{{ request()->routeIs('admin.expense-categories.*') ? 'active' : '' }}">
                <a href="{{ route('admin.expense-categories.index') }}">
                    <x-lucide-folder-open class="nav-icon" /> {{-- Or other icon --}}
                    <span class="nav-text">اقسام المصاريف</span>
                </a>
            </li>
             <li class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <a href="#"> {{-- Replace with route('reports.index') later --}}
                    <x-lucide-bar-chart-3 class="nav-icon" />
                    <span class="nav-text">التقارير</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                <a href="{{ route('admin.payments.index') }}">
                    <x-lucide-landmark class="nav-icon" /> {{-- Or wallet --}}
                    <span class="nav-text">سجلات الدفع</span>
                </a>
            </li>

            {{-- Frontend Management Section --}}
            <li class="nav-section-title"><span class="nav-text">إدارة الواجهة الأمامية</span></li>
            <li class="{{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                <a href="{{ route('admin.banners.index') }}">
                    <x-lucide-image class="nav-icon" />
                    <span class="nav-text">البانرات (Slider)</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }}">
                <a href="{{ route('admin.testimonials.index') }}">
                    <x-lucide-message-square-quote class="nav-icon" />
                    <span class="nav-text">تقييمات العملاء</span>
                </a>
            </li>


            {{-- System Settings --}}
            <li class="nav-section-title"><span class="nav-text">النظام</span></li>
            <li class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <a href="{{ route('admin.settings.index') }}"> {{-- Updated Route --}}
                    <x-lucide-settings class="nav-icon" />
                    <span class="nav-text">الاعدادات</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.languages.*') ? 'active' : '' }}">
                <a href="{{ route('admin.languages.index') }}">
                    <x-lucide-languages class="nav-icon" />
                    <span class="nav-text">اللغات</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.seo.*') ? 'active' : '' }}">
                <a href="{{ route('admin.seo.index') }}">
                    <x-lucide-search-code class="nav-icon" /> {{-- Example icon --}}
                    <span class="nav-text">تحسين محركات البحث</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.policies.*') ? 'active' : '' }}">
                <a href="{{ route('admin.policies.index') }}">
                    <x-lucide-shield-check class="nav-icon" /> {{-- Example icon --}}
                    <span class="nav-text">السياسات</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.contact-messages.*') ? 'active' : '' }}">
                <a href="{{ route('admin.contact-messages.index') }}">
                    <x-lucide-mail class="nav-icon" />
                    <span class="nav-text">رسائل التواصل</span>
                    @inject('contactMessageCounter', 'App\Models\ContactMessage') {{-- Inject model for count --}}
                    @if($unreadCount = $contactMessageCounter::unread()->count())
                        <span class="badge bg-danger rounded-pill ms-auto">{{ $unreadCount }}</span> {{-- Unread count badge --}}
                    @endif
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.templates.*') ? 'active' : '' }}">
                <a href="{{ route('admin.templates.index') }}">
                    <x-lucide-mail-check class="nav-icon" /> {{-- Or palette --}}
                    <span class="nav-text">قوالب البريد الالكتروني</span>
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
                    <x-lucide-log-out class="nav-icon" style="width: 20px; height: 20px;" />
                    <span class="nav-text">تسجيل الخروج</span>
               </a>
         </form>
    </div>
</aside>