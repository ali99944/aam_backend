@extends('layouts.admin')
@section('title', 'Action Requests')

@push('styles')
<style>
/* Status badge styles */
.status-badge { font-size: 0.8em; padding: 3px 8px; border-radius: 5px; font-weight: 500;}
.status-badge-pending { background-color: #fff3cd; color: #664d03; border: 1px solid #ffeeba;}
.status-badge-approved { background-color: #d1e7dd; color: #0f5132; }
.status-badge-rejected { background-color: #f8d7da; color: #842029; }
.action-data-preview {
    font-family: monospace;
    font-size: 0.85em;
    background-color: #f8f9fa;
    padding: 5px 8px;
    border-radius: 4px;
    max-height: 60px;
    overflow: hidden;
    display: block;
    white-space: pre;
    word-break: break-all;
    cursor: pointer; /* Indicate it's expandable */
}
.modal-body pre { background-color: #efefef; padding: 10px; border-radius: 4px; max-height: 400px; overflow: auto;}
.action-time { font-size: 0.85em; color: #6c757d; }
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>Action Requests</h1>
        <div class="actions">
            <a href="{{ route('admin.action-requests.create') }}" class="btn btn-outline-primary">
                <x-lucide-plus class="icon-sm mr-1"/> Create Manual Request
            </a>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="card mb-4">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.action-requests.index') }}" class="form-inline flex-wrap">
                 {{-- Search --}}
                 <div class="form-group mr-2 mb-2">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search ID or Type..." value="{{ request('search') }}">
                </div>
                 {{-- Status --}}
                 <div class="form-group mr-2 mb-2">
                    <select name="status" class="form-select form-select-sm">
                         <option value="all">All Statuses</option>
                         @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ request('status', 'pending') == $key ? 'selected' : '' }}>{{ $label }}</option> {{-- Default to Pending --}}
                         @endforeach
                    </select>
                </div>
                 {{-- Action Type --}}
                 <div class="form-group mr-2 mb-2">
                    <select name="action_type" class="form-select form-select-sm">
                         <option value="all">All Action Types</option>
                         @foreach($actionTypes as $key => $label)
                            <option value="{{ $key }}" {{ request('action_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                         @endforeach
                    </select>
                </div>
                <div class="form-group mb-2">
                    <button type="submit" class="btn btn-secondary btn-sm">
                        <x-lucide-filter class="icon-sm mr-1"/> Filter
                    </button>
                     @if(request()->hasAny(['search', 'status', 'action_type']))
                        <a href="{{ route('admin.action-requests.index', ['status' => 'pending']) }}" class="btn btn-link btn-sm ml-1">Clear & Show Pending</a>
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
                            <th>ID</th>
                            <th>Action Type</th>
                            <th>Data Preview</th>
                            <th>Requested By</th>
                            <th>Requested At</th>
                            <th>Status</th>
                            <th>Processed By</th>
                            <th>Processed At</th>
                            <th>Actions</th>
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
                                     <code class="action-data-preview" title="Click to view full data"
                                           data-bs-toggle="modal" data-bs-target="#dataPreviewModal"
                                           data-request-id="{{ $actionRequest->id }}"
                                           data-request-type="{{ $actionRequest->action_type_name }}"
                                           data-request-data="{{ json_encode($actionRequest->data, JSON_PRETTY_PRINT) }}">
                                        {{ Str::limit(json_encode($actionRequest->data), 60) }}
                                     </code>
                                </td>
                                <td>{{ $actionRequest->requestor->name ?? 'N/A' }}</td>
                                <td class="action-time">{{ $actionRequest->created_at->diffForHumans() }}</td>
                                <td>
                                    <span class="status-badge status-badge-{{ $actionRequest->status }}">
                                        {{ $statuses[$actionRequest->status] ?? ucfirst($actionRequest->status) }}
                                    </span>
                                    @if($actionRequest->status == 'rejected' && $actionRequest->rejection_reason)
                                        <small class="d-block text-danger" title="{{ $actionRequest->rejection_reason }}">
                                            <x-lucide-info size="14"/> {{ Str::limit($actionRequest->rejection_reason, 30) }}
                                        </small>
                                    @endif
                                </td>
                                <td>{{ $actionRequest->processor->name ?? '-' }}</td>
                                <td class="action-time">{{ $actionRequest->processed_at?->diffForHumans() ?? '-' }}</td>
                                <td class="actions">
                                    @if($actionRequest->status === 'pending')
                                        {{-- Approve Button --}}
                                        <form action="{{ route('admin.action-requests.process', $actionRequest->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Approve this request?');">
                                            @csrf
                                             {{-- @method('PATCH') or @method('PUT') --}}
                                            <input type="hidden" name="process_action" value="approve">
                                            <button type="submit" class="btn btn-sm btn-success" title="Approve Request">
                                                <x-lucide-check /> Approve
                                            </button>
                                        </form>
                                         {{-- Reject Button (triggers modal) --}}
                                         <button type="button" class="btn btn-sm btn-danger reject-btn"
                                                data-bs-toggle="modal" data-bs-target="#rejectModal"
                                                data-request-id="{{ $actionRequest->id }}"
                                                data-request-type-name="{{ $actionRequest->action_type_name }}"
                                                title="Reject Request">
                                            <x-lucide-x /> Reject
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
                                        <span class="text-muted">Processed</span>
                                    @endif
                                     {{-- Optional: Add Delete button for specific statuses/roles --}}
                                     {{-- <form action="{{ route('admin.action-requests.destroy', $actionRequest->id) }}" method="POST" ...> ... </form> --}}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center py-4">No action requests found.</td></tr>
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
        <div class="modal-dialog modal-lg"> {{-- Larger modal --}}
            <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="dataPreviewModalLabel">Data for Request #<span id="previewRequestId"></span> (<span id="previewRequestType"></span>)</h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                 </div>
                 <div class="modal-body">
                     <pre id="previewRequestDataJson"></pre>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                     {{-- @method('PATCH') or @method('PUT') --}}
                    <input type="hidden" name="process_action" value="reject">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectModalLabel">Reject Request #<span id="rejectRequestId"></span> (<span id="rejectRequestTypeName"></span>)</h5>
                         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                             <label for="rejection_reason" class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" required></textarea>
                         </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Confirm Rejection</button>
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
 document.addEventListener('DOMContentLoaded', function () {
    // --- Data Preview Modal ---
    const dataPreviewModal = document.getElementById('dataPreviewModal');
    if(dataPreviewModal) {
        const previewRequestIdSpan = document.getElementById('previewRequestId');
        const previewRequestTypeSpan = document.getElementById('previewRequestType');
        const previewRequestDataJsonPre = document.getElementById('previewRequestDataJson');

        dataPreviewModal.addEventListener('show.bs.modal', function (event) {
            const triggerElement = event.relatedTarget; // Element that triggered modal
            const requestId = triggerElement.getAttribute('data-request-id');
            const requestType = triggerElement.getAttribute('data-request-type');
            const requestData = triggerElement.getAttribute('data-request-data');

            previewRequestIdSpan.textContent = requestId;
            previewRequestTypeSpan.textContent = requestType;
            previewRequestDataJsonPre.textContent = requestData; // Already formatted JSON
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
             rejectReasonTextarea.value = ''; // Clear previous reason
             // Set form action dynamically
             rejectForm.action = `/admin/action-requests/${requestId}/process`; // Adjust URL if needed
         });
     }

 });
</script>
@endpush