@extends('layouts.admin')
@section('title', 'Contact Messages')

@push('styles')
<style>
.message-read { background-color: #f8f9fa; }
.message-unread td:first-child { border-left: 3px solid var(--primary-color); } /* LTR */
html[dir="rtl"] .message-unread td:first-child { border-left: none; border-right: 3px solid var(--primary-color); }
.subject-link { color: var(--dark-color); font-weight: 500; text-decoration: none; }
.subject-link:hover { color: var(--primary-color); }
.message-unread .subject-link { font-weight: 700; }
</style>
@endpush


@section('content')
    <div class="content-header">
        <h1>Contact Messages</h1>
         {{-- Unread Count shown in sidebar link now --}}
    </div>

    {{-- Filter Form --}}
    <div class="card mb-4">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.contact-messages.index') }}" class="form-inline">
                 <div class="form-group mr-2">
                    <label for="filter_status" class="mr-1">Show:</label>
                    <select id="filter_status" name="filter" class="form-control form-control-sm" onchange="this.form.submit()">
                         <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>All Messages</option>
                         <option value="unread" {{ request('filter') == 'unread' ? 'selected' : '' }}>Unread Only</option>
                         <option value="read" {{ request('filter') == 'read' ? 'selected' : '' }}>Read Only</option>
                    </select>
                </div>
                {{-- Add search input if needed --}}
                {{--
                <div class="form-group mr-2">
                     <input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-secondary btn-sm">Search</button>
                --}}
                 @if(request('filter') && request('filter') != 'all')
                    <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-link btn-sm ml-1">Clear Filter</a>
                 @endif
            </form>
        </div>
    </div>

    <div class="card">
         <div class="card-body p-0">
             <div class="table-responsive">
                 <table class="admin-table">
                     <thead>
                         <tr>
                             <th style="width: 25%;">Sender</th>
                             <th>Subject</th>
                             <th style="width: 20%;">Received</th>
                             <th style="width: 10%;">Actions</th>
                         </tr>
                     </thead>
                     <tbody>
                         @forelse ($messages as $message)
                             <tr class="{{ $message->is_read ? 'message-read' : 'message-unread' }}">
                                 <td>
                                     <strong>{{ $message->name }}</strong>
                                     <small class="d-block text-muted">{{ $message->email }}</small>
                                     @if($message->phone)
                                         <small class="d-block text-muted"><x-lucide-phone class="icon-xs"/> {{ $message->phone }}</small>
                                     @endif
                                 </td>
                                 <td>
                                     <a href="{{ route('admin.contact-messages.show', $message->id) }}" class="subject-link">
                                         {{ $message->subject ?? '(No Subject)' }}
                                     </a>
                                     <p class="mb-0 text-muted"><small>{{ Str::limit($message->message, 100) }}</small></p>
                                 </td>
                                 <td>
                                     {{ $message->created_at->diffForHumans() }}
                                     <small class="d-block text-muted">{{ $message->created_at->format('d M Y, H:i') }}</small>
                                 </td>
                                 <td class="actions">
                                     <a href="{{ route('admin.contact-messages.show', $message->id) }}" class="btn btn-sm btn-outline-info" title="View">
                                         <x-lucide-eye />
                                     </a>
                                     <form action="{{ route('admin.contact-messages.destroy', $message->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                         @csrf
                                         @method('DELETE')
                                         <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                             <x-lucide-trash-2 />
                                         </button>
                                     </form>
                                      {{-- Optional Mark Unread Button --}}
                                      {{-- @if($message->is_read)
                                        <form action="{{ route('admin.contact-messages.markUnread', $message->id) }}" method="POST" class="d-inline-block">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Mark as Unread"><x-lucide-mail-open/></button>
                                        </form>
                                      @endif --}}
                                 </td>
                             </tr>
                         @empty
                             <tr>
                                 <td colspan="4" class="text-center py-4">No contact messages found.</td>
                             </tr>
                         @endforelse
                     </tbody>
                 </table>
             </div>
         </div>
         @if ($messages->hasPages())
            <div class="card-footer">
                 {{ $messages->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection