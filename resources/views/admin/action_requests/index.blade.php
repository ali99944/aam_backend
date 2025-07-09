@extends('layouts.admin')
@section('title', 'طلبات الإجراءات') {{-- Action Requests --}}

@push('styles')
<style>
/* Status badge styles (Can be reused from other views or placed in common.css) */
.status-badge { font-size: 0.8em; padding: 3px 8px; border-radius: 5px; font-weight: 500;}
.status-badge-pending { background-color: #fff3cd; color: #664d03; border: 1px solid #ffeeba;} /* قيد الانتظار */
.status-badge-approved { background-color: #d1e7dd; color: #0f5132; } /* تمت الموافقة */
.status-badge-rejected { background-color: #f8d7da; color: #842029; } /* مرفوض */

.action-data-preview {
    font-family: monospace; /* Keeps JSON structure readable */
    font-size: 0.85em;
    background-color: #f8f9fa;
    padding: 5px 8px;
    border-radius: 4px;
    max-height: 60px;
    overflow: hidden;
    display: block;
    white-space: pre; /* Important for JSON preview */
    word-break: break-all;
    cursor: pointer;
    text-align: left; /* Ensure JSON is LTR even in RTL page */
    direction: ltr; /* Ensure JSON is LTR */
}
.modal-body pre {
    background-color: #efefef; padding: 10px; border-radius: 4px;
    max-height: 400px; overflow: auto;
    text-align: left; /* LTR for JSON */
    direction: ltr;   /* LTR for JSON */
}
.action-time { font-size: 0.85em; color: #6c757d; }
/* RTL adjustments for form-inline if not globally handled */
html[dir="rtl"] .form-inline .form-group.mr-2 { margin-right: 0; margin-left: 0.5rem; }
html[dir="rtl"] .form-inline .btn-link.ml-1 { margin-left: 0; margin-right: 0.25rem; }
html[dir="rtl"] .mr-1 { margin-right: 0 !important; margin-left: 0.25rem !important; }
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>إدارة طلبات الإجراءات</h1>
        <div class="actions">
            <a href="{{ route('admin.action-requests.create') }}" class="btn btn-outline-primary">
                <x-lucide-plus class="icon-sm ms-1"/> إنشاء طلب يدوي {{-- Flipped icon for RTL --}}
            </a>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="card mb-4">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.action-requests.index') }}" class="form-inline flex-wrap">
                 {{-- Search --}}
                 <div class="form-group mr-2 mb-2"> {{-- mr-2 becomes ml-2 in RTL via CSS or inline style --}}
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="بحث بالرقم أو النوع..." value="{{ request('search') }}">
                </div>
                 {{-- Status --}}
                 <div class="form-group mr-2 mb-2">
                    <select name="status" class="form-select form-select-sm">
                         <option value="all">كل الحالات</option>
                         @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ request('status', 'pending') == $key ? 'selected' : '' }}>{{ $label }}</option>
                         @endforeach
                    </select>
                </div>
                 {{-- Action Type --}}
                 <div class="form-group mr-2 mb-2">
                    <select name="action_type" class="form-select form-select-sm">
                         <option value="all">كل أنواع الإجراءات</option>
                         @foreach($actionTypes as $key => $label)
                            <option value="{{ $key }}" {{ request('action_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                         @endforeach
                    </select>
                </div>
                <div class="form-group mb-2">
                    <button type="submit" class="btn btn-secondary btn-sm">
                        <x-lucide-filter class="icon-sm ms-1"/> تصفية
                    </button>
                     @if(request()->hasAny(['search', 'status', 'action_type']))
                        <a href="{{ route('admin.action-requests.index', ['status' => 'pending']) }}" class="btn btn-link btn-sm ml-1">مسح & عرض قيد الانتظار</a>
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
                            <th>الرقم</th>
                            <th>نوع الإجراء</th>
                            <th>معاينة البيانات</th>
                            <th>مقدم الطلب</th>
                            <th>تاريخ الطلب</th>
                            <th>الحالة</th>
                            <th>مُعالج بواسطة</th>
                            <th>تاريخ المعالجة</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($requests as $actionRequest)
                            <tr>
                                <td>#{{ $actionRequest->id }}</td>
                                <td>
                                    <strong>{{ $actionRequest->action_type_name }}</strong>
                                    <code class="d-block"><small>{{ $actionRequest->action_type }}</small></code>
                                </td>
                                <td>
                                     <code class="action-data-preview" title="انقر لعرض البيانات كاملة"
                                           data-bs-toggle="modal" data-bs-target="#dataPreviewModal"
                                           data-request-id="{{ $actionRequest->id }}"
                                           data-request-type="{{ $actionRequest->action_type_name }}"
                                           data-request-data="{{ json_encode($actionRequest->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}"> {{-- Added JSON_UNESCAPED_UNICODE --}}
                                        {{ Str::limit(json_encode($actionRequest->data, JSON_UNESCAPED_UNICODE), 60) }}
                                     </code>
                                </td>
                                <td>{{ $actionRequest->requestor->name ?? 'غير متوفر' }}</td>
                                <td class="action-time">{{ $actionRequest->created_at->locale('ar')->diffForHumans() }}</td> {{-- Arabic diffForHumans --}}
                                <td>
                                    <span class="status-badge status-badge-{{ $actionRequest->status }}">
                                        {{ $statuses[$actionRequest->status] ?? ucfirst($actionRequest->status) }}
                                    </span>
                                    @if($actionRequest->status == 'rejected' && $actionRequest->rejection_reason)
                                        <small class="d-block text-danger" title="{{ $actionRequest->rejection_reason }}">
                                            <x-lucide-info size="14" class="ms-1"/> {{ Str::limit($actionRequest->rejection_reason, 30) }}
                                        </small>
                                    @endif
                                </td>
                                <td>{{ $actionRequest->processor->name ?? '-' }}</td>
                                <td class="action-time">{{ $actionRequest->processed_at?->locale('ar')->diffForHumans() ?? '-' }}</td>
                                <td class="actions ws-nowrap"> {{-- Added ws-nowrap for buttons --}}
                                    @if($actionRequest->status === 'pending')
                                        {{-- Approve Button --}}
                                        <form action="{{ route('admin.action-requests.process', $actionRequest->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل أنت متأكد من الموافقة على هذا الطلب؟');">
                                            @csrf
                                            <input type="hidden" name="process_action" value="approve">
                                            <button type="submit" class="btn btn-sm btn-success" title="الموافقة على الطلب">
                                                <x-lucide-check /> موافقة
                                            </button>
                                        </form>
                                         {{-- Reject Button (triggers modal) --}}
                                         <button type="button" class="btn btn-sm btn-danger reject-btn"
                                                data-bs-toggle="modal" data-bs-target="#rejectModal"
                                                data-request-id="{{ $actionRequest->id }}"
                                                data-request-type-name="{{ $actionRequest->action_type_name }}"
                                                title="رفض الطلب">
                                            <x-lucide-x /> رفض
                                        </button>
                                         {{-- Display processing errors for this specific request --}}
                                        @if($errors->hasBag('process_' . $actionRequest->id))
                                            <div class="text-danger mt-1">
                                                 @foreach ($errors->getBag('process_' . $actionRequest->id)->all() as $error)
                                                    <small>{{ $error }}</small><br>
                                                 @endforeach
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-muted">تمت المعالجة</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center py-4">لم يتم العثور على طلبات إجراءات.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($requests->hasPages())
            <div class="card-footer">
                 {{ $requests->appends(request()->query())->links() }}
            </div>
        @endif
    </div>


    {{-- Data Preview Modal --}}
    <div class="modal fade" id="dataPreviewModal" tabindex="-1" aria-labelledby="dataPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="dataPreviewModalLabel">بيانات الطلب رقم #<span id="previewRequestId"></span> (<span id="previewRequestType"></span>)</h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                 </div>
                 <div class="modal-body">
                     <pre id="previewRequestDataJson"></pre>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                 </div>
            </div>
        </div>
    </div>

     {{-- Rejection Reason Modal --}}
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="rejectForm" method="POST" action=""> {{-- Action set by JS --}}
                    @csrf
                    <input type="hidden" name="process_action" value="reject">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectModalLabel">رفض الطلب رقم #<span id="rejectRequestId"></span> (<span id="rejectRequestTypeName"></span>)</h5>
                         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                             <label for="rejection_reason" class="form-label">سبب الرفض <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" required></textarea>
                         </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger">تأكيد الرفض</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
 {{-- Ensure Bootstrap JS is loaded for modals --}}
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
 // Keep the JavaScript for modals the same as your previous version,
 // as it correctly handles dynamic content and form actions.
 // No translation needed for the JS logic itself, only for displayed text if any.
 document.addEventListener('DOMContentLoaded', function () {
    // --- Data Preview Modal ---
    const dataPreviewModal = document.getElementById('dataPreviewModal');
    if(dataPreviewModal) {
        const previewRequestIdSpan = document.getElementById('previewRequestId');
        const previewRequestTypeSpan = document.getElementById('previewRequestType');
        const previewRequestDataJsonPre = document.getElementById('previewRequestDataJson');

        dataPreviewModal.addEventListener('show.bs.modal', function (event) {
            const triggerElement = event.relatedTarget;
            const requestId = triggerElement.getAttribute('data-request-id');
            const requestType = triggerElement.getAttribute('data-request-type');
            const requestData = triggerElement.getAttribute('data-request-data');

            previewRequestIdSpan.textContent = requestId;
            previewRequestTypeSpan.textContent = requestType;
            try {
                // Attempt to parse and re-stringify with pretty print if not already done,
                // but the data-attribute already has it pretty printed.
                previewRequestDataJsonPre.textContent = JSON.stringify(JSON.parse(requestData), null, 2);
            } catch (e) {
                previewRequestDataJsonPre.textContent = requestData; // Fallback to raw data if parse fails
            }
        });
    }

    // --- Rejection Modal ---
     const rejectModal = document.getElementById('rejectModal');
     if(rejectModal){
         const rejectForm = document.getElementById('rejectForm');
         const rejectRequestIdSpan = document.getElementById('rejectRequestId');
         const rejectRequestTypeNameSpan = document.getElementById('rejectRequestTypeName');
         const rejectReasonTextarea = document.getElementById('rejection_reason');

         rejectModal.addEventListener('show.bs.modal', function (event) {
             const button = event.relatedTarget;
             const requestId = button.getAttribute('data-request-id');
             const requestTypeName = button.getAttribute('data-request-type-name');

             rejectRequestIdSpan.textContent = requestId;
             rejectRequestTypeNameSpan.textContent = requestTypeName;
             rejectReasonTextarea.value = '';
             rejectForm.action = `/admin/action-requests/${requestId}/process`;
         });
     }
 });
</script>
@endpush