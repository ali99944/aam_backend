@extends('layouts.admin')
@section('title', 'رسائل التواصل')

@push('styles')
<style>
.message-read { background-color: #f8f9fa; }
.message-unread td:first-child { border-left: 3px solid var(--primary-color); } /* LTR */
html[dir="rtl"] .message-unread td:first-child { border-left: none; border-right: 3px solid var(--primary-color); }
.subject-link { color: var(--dark-color); font-weight: 500; text-decoration: none; }
.subject-link:hover { color: var(--primary-color); }
.message-unread .subject-link { font-weight: 700; }

/* RTL Adjustments for form-inline & icons if not globally handled */
html[dir="rtl"] .form-inline .form-group.mr-2 { margin-right: 0 !important; margin-left: 0.5rem !important; }
html[dir="rtl"] .form-inline label.mr-1 { margin-right: 0 !important; margin-left: 0.25rem !important; }
html[dir="rtl"] .form-inline .btn-link.ml-1 { margin-left: 0 !important; margin-right: 0.25rem !important; }
html[dir="rtl"] .icon-xs { margin-right: 0 !important; margin-left: 0.25rem !important; } /* Adjust icon margin for RTL */
</style>
@endpush


@section('content')
    <div class="content-header">
        <h1>رسائل التواصل</h1>
         {{-- Unread Count shown in sidebar link now --}}
    </div>

    {{-- Filter Form --}}
    <div class="card mb-4">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.contact-messages.index') }}" class="form-inline">
                 <div class="form-group mr-2"> {{-- mr-2 becomes ml-2 in RTL via CSS or global Bootstrap RTL --}}
                    <label for="filter_status" class="mr-1">عرض:</label> {{-- mr-1 becomes ml-1 in RTL --}}
                    <select id="filter_status" name="filter" class="form-control form-control-sm" onchange="this.form.submit()">
                         <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>كل الرسائل</option>
                         <option value="unread" {{ request('filter') == 'unread' ? 'selected' : '' }}>غير المقروءة فقط</option>
                         <option value="read" {{ request('filter') == 'read' ? 'selected' : '' }}>المقروءة فقط</option>
                    </select>
                </div>
                {{-- Add search input if needed --}}
                {{--
                <div class="form-group mr-2">
                     <input type="text" name="search" class="form-control form-control-sm" placeholder="بحث..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-secondary btn-sm">بحث</button>
                --}}
                 @if(request('filter') && request('filter') != 'all')
                    <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-link btn-sm ml-1">مسح الفلتر</a> {{-- ml-1 becomes mr-1 in RTL --}}
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
                             <th style="width: 25%;">المرسل</th>
                             <th>الموضوع</th>
                             <th style="width: 20%;">تاريخ الاستلام</th>
                             <th style="width: 10%;">الإجراءات</th>
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
                                         {{ $message->subject ?? '(بدون موضوع)' }}
                                     </a>
                                     <p class="mb-0 text-muted"><small>{{ Str::limit($message->message, 100) }}</small></p>
                                 </td>
                                 <td>
                                     {{ $message->created_at->locale('ar')->diffForHumans() }} {{-- Arabic relative time --}}
                                     <small class="d-block text-muted">{{ $message->created_at->locale('ar')->translatedFormat('d M Y, H:i') }}</small> {{-- Arabic date format --}}
                                 </td>
                                 <td class="actions">
                                     <a href="{{ route('admin.contact-messages.show', $message->id) }}" class="btn btn-sm btn-outline-info" title="عرض">
                                         <x-lucide-eye />
                                     </a>
                                     <form action="{{ route('admin.contact-messages.destroy', $message->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل أنت متأكد من حذف هذه الرسالة؟');">
                                         @csrf
                                         @method('DELETE')
                                         <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                             <x-lucide-trash-2 />
                                         </button>
                                     </form>
                                      {{-- Optional Mark Unread Button --}}
                                      {{-- @if($message->is_read)
                                        <form action="{{ route('admin.contact-messages.markUnread', $message->id) }}" method="POST" class="d-inline-block">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="تحديد كغير مقروءة"><x-lucide-mail-open/></button>
                                        </form>
                                      @endif --}}
                                 </td>
                             </tr>
                         @empty
                             <tr>
                                 <td colspan="4" class="text-center py-4">لم يتم العثور على رسائل تواصل.</td>
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