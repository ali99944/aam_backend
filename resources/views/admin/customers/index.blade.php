@extends('layouts.admin')
@section('title', 'إدارة العملاء')

@push('styles')
<style>
/* Status badge styles */
.status-badge { font-size: 0.8em; padding: 3px 8px; border-radius: 5px; font-weight: 500;}
.status-badge-active { background-color: #d1e7dd; color: #0f5132; } /* فعال */
.status-badge-banned { background-color: #f8d7da; color: #842029; } /* محظور */
.status-badge-verification-required { background-color: #fff3cd; color: #664d03; border: 1px solid #ffeeba;} /* بانتظار التحقق */
.verification-icon { vertical-align: middle; }
.ban-info { font-size: 0.85em; color: #6c757d; }
.modal-body .form-label { font-weight: 500; }

/* Plain JS Modal CSS (from previous response) */
.modal-overlay {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background-color: rgba(0, 0, 0, 0.5); display: none; opacity: 0;
    transition: opacity 0.3s ease; z-index: 1050; align-items: center;
    justify-content: center; padding: 15px;
}
.modal-overlay.active { display: flex; opacity: 1; }
.modal-container {
    background-color: #fff; border-radius: 5px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    max-width: 500px; width: 100%; opacity: 0; transform: translateY(-20px);
    transition: opacity 0.3s ease, transform 0.3s ease; z-index: 1051;
    display: flex; flex-direction: column; max-height: calc(100vh - 40px);
}
.modal-overlay.active .modal-container { opacity: 1; transform: translateY(0); }
.modal-header {
    display: flex; justify-content: space-between; align-items: center;
    padding: 1rem 1rem; border-bottom: 1px solid #dee2e6;
}
.modal-header h5 { margin-bottom: 0; font-size: 1.25rem; font-weight: 500; }
.modal-close-btn {
    background: transparent; border: none; font-size: 1.5rem; line-height: 1;
    opacity: 0.5; padding: 0.5rem; margin: -0.5rem -0.5rem -0.5rem auto; cursor: pointer;
}
html[dir="rtl"] .modal-close-btn { margin: -0.5rem auto -0.5rem -0.5rem; } /* Adjust close button for RTL modal */
.modal-close-btn:hover { opacity: 0.8; }
.modal-body { position: relative; flex: 1 1 auto; padding: 1rem; overflow-y: auto; }
.modal-footer {
    display: flex; flex-wrap: wrap; align-items: center; justify-content: flex-end;
    padding: 0.75rem; border-top: 1px solid #dee2e6;
}
html[dir="rtl"] .modal-footer { justify-content: flex-start; } /* Align footer buttons left in RTL */
.modal-footer > * { margin: 0.25rem; }

/* RTL Adjustments for form-inline & icons if not globally handled */
html[dir="rtl"] .form-inline .form-group.mr-2 { margin-right: 0 !important; margin-left: 0.5rem !important; }
html[dir="rtl"] .form-inline label.mr-1 { margin-right: 0 !important; margin-left: 0.25rem !important; }
html[dir="rtl"] .form-inline .btn-link.ml-1 { margin-left: 0 !important; margin-right: 0.25rem !important; }
html[dir="rtl"] .mr-1 { margin-right: 0 !important; margin-left: 0.25rem !important; }
html[dir="rtl"] .ms-1 { margin-left: 0 !important; margin-right: 0.25rem !important; } /* For icon on right */
.ws-nowrap { white-space: nowrap; }
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>إدارة العملاء</h1>
        {{-- Optional: Add "Add Customer" button --}}
    </div>

    {{-- Filter Form --}}
    <div class="card mb-4">
         <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.customers.index') }}" class="form-inline flex-wrap">
                 {{-- Search --}}
                 <div class="form-group mr-2 mb-2">
                     <label for="search" class="mr-1 d-none d-sm-inline">بحث:</label>
                    <input type="text" id="search" name="search" class="form-control form-control-sm" placeholder="الاسم، البريد، الهاتف..." value="{{ request('search') }}">
                </div>
                 {{-- Status --}}
                 <div class="form-group mr-2 mb-2">
                    <label for="filter_status" class="mr-1">الحالة:</label>
                    <select id="filter_status" name="status" class="form-control form-control-sm">
                         <option value="all">كل الحالات</option>
                         @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                         @endforeach
                    </select>
                </div>
                 {{-- Banned Status --}}
                 <div class="form-group mr-2 mb-2">
                    <label for="filter_banned" class="mr-1">محظور:</label>
                    <select id="filter_banned" name="banned" class="form-control form-control-sm">
                         <option value="all">الكل</option>
                         <option value="1" {{ request('banned') === '1' ? 'selected' : '' }}>نعم</option>
                         <option value="0" {{ request('banned') === '0' ? 'selected' : '' }}>لا</option>
                    </select>
                </div>
                {{-- Submit --}}
                <div class="form-group mb-2">
                    <button type="submit" class="btn btn-secondary btn-sm">
                        <x-lucide-filter class="icon-sm ms-1"/> تصفية
                    </button>
                     @if(request()->hasAny(['search', 'status', 'banned']))
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-link btn-sm ml-1">مسح الفلاتر</a>
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
                            <th>الاسم</th>
                            <th>معلومات الاتصال</th>
                            <th>الحالة</th>
                            <th>محظور</th>
                            <th>الرصيد</th>
                            <th>تاريخ التسجيل</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $customer)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.customers.show', $customer->id) }}" class="fw-bold">
                                        {{ $customer->name }}
                                    </a>
                                </td>
                                <td>
                                    <div>{{ $customer->email }}</div>
                                    <small class="text-muted">{{ $customer->phone }}</small>
                                </td>
                                <td>
                                    <span class="status-badge status-badge-{{ str_replace('_','-',$customer->status) }}">
                                        {{ $statuses[$customer->status] ?? ucfirst($customer->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($customer->is_banned)
                                        <span class="text-danger">نعم</span>
                                        <small class="d-block ban-info" title="{{ $customer->ban_reason }}">
                                            بتاريخ: {{ $customer->banned_at?->locale('ar')->translatedFormat('d M Y') }}
                                        </small>
                                    @else
                                        <span class="text-muted">لا</span>
                                    @endif
                                </td>
                                <td>AED {{ number_format($customer->balance, 2) }}</td>
                                <td>{{ $customer->created_at->locale('ar')->translatedFormat('d M Y') }}</td>
                                <td class="actions ws-nowrap">
                                     <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-sm btn-outline-info" title="عرض التفاصيل">
                                        <x-lucide-eye />
                                    </a>
                                    @if(!$customer->is_banned)
                                        <button type="button" class="btn btn-sm btn-outline-warning ban-btn"
                                                data-customer-id="{{ $customer->id }}"
                                                data-customer-name="{{ $customer->name }}"
                                                title="حظر العميل">
                                            <x-lucide-user-x />
                                        </button>
                                    @else
                                         <form action="{{ route('admin.customers.unban', $customer->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل تريد إلغاء حظر {{ $customer->name }}؟');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="إلغاء حظر العميل">
                                                <x-lucide-user-check />
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل تريد حذف العميل {{ $customer->name }}؟ هذا الإجراء دائم!');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف العميل">
                                            <x-lucide-trash-2 />
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">لم يتم العثور على عملاء.</td>
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
    <div class="modal-overlay" id="banModalOverlay">
        <div class="modal-container">
            <form id="banForm" method="POST" action="">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">حظر العميل: <span id="banCustomerName"></span></h5>
                    <button type="button" class="modal-close-btn" aria-label="إغلاق">×</button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="ban_reason" class="form-label">سبب الحظر <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="ban_reason" name="ban_reason" rows="4" required></textarea>
                        <small class="text-muted">قد يتم عرض هذا السبب للعميل أو استخدامه للسجلات الداخلية.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-cancel-btn">إلغاء</button>
                    <button type="submit" class="btn btn-danger">تأكيد الحظر</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
{{-- Removed Bootstrap JS for modal --}}
<script>
    // Plain JS Modal script from previous response (no translation needed for this logic)
    document.addEventListener('DOMContentLoaded', function () {
        const banModalOverlay = document.getElementById('banModalOverlay');
        if (!banModalOverlay) return; // Exit if modal HTML not present

        const banForm = document.getElementById('banForm');
        const banCustomerNameSpan = document.getElementById('banCustomerName');
        const banReasonTextarea = document.getElementById('ban_reason');
        const banButtons = document.querySelectorAll('.ban-btn');
        const closeButtons = banModalOverlay.querySelectorAll('.modal-close-btn, .modal-cancel-btn');

        function openModal(customerId, customerName) {
            if (!banForm || !banCustomerNameSpan || !banReasonTextarea) return;
            banCustomerNameSpan.textContent = customerName;
            banForm.setAttribute('action', `/admin/customers/${customerId}/ban`); // Adjust if needed
            banReasonTextarea.value = '';
            banModalOverlay.classList.add('active');
            document.addEventListener('keydown', handleEscapeKey);
        }

        function closeModal() {
            banModalOverlay.classList.remove('active');
            document.removeEventListener('keydown', handleEscapeKey);
        }

        function handleEscapeKey(event) {
            if (event.key === 'Escape') closeModal();
        }

        banButtons.forEach(button => {
            button.addEventListener('click', function() {
                openModal(this.getAttribute('data-customer-id'), this.getAttribute('data-customer-name'));
            });
        });

        closeButtons.forEach(button => button.addEventListener('click', closeModal));

        banModalOverlay.addEventListener('click', function(event) {
            if (event.target === banModalOverlay) closeModal();
        });
    });
</script>
@endpush