@extends('layouts.admin')
@section('title', 'View Contact Message')

@section('content')
    <div class="content-header">
        <h1>Contact Message Details</h1>
         <div class="actions">
             <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-secondary">
                 <x-lucide-arrow-left class="icon-sm mr-1"/> Back to Messages
             </a>
         </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="mb-0">Subject: {{ $contactMessage->subject ?? '(No Subject)' }}</h3>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>From:</strong> {{ $contactMessage->name }}</p>
                    <p class="mb-0"><strong>Email:</strong> <a href="mailto:{{ $contactMessage->email }}">{{ $contactMessage->email }}</a></p>
                </div>
                <div class="col-md-6 text-md-end">
                     @if($contactMessage->phone)
                        <p><strong>Phone:</strong> {{ $contactMessage->phone }}</p>
                     @endif
                     <p class="mb-0"><small><strong>Received:</strong> {{ $contactMessage->created_at->format('d M Y, H:i A') }} ({{ $contactMessage->created_at->diffForHumans() }})</small></p>
                </div>
            </div>

            <hr>

            <div class="message-content mt-3">
                <p><strong>Message:</strong></p>
                {{-- Use nl2br to preserve line breaks --}}
                <p style="white-space: pre-wrap;">{{ $contactMessage->message }}</p>
            </div>
        </div>
        <div class="card-footer text-end">
             {{-- Optional Mark Unread Button --}}
            {{--
            <form action="{{ route('admin.contact-messages.markUnread', $contactMessage->id) }}" method="POST" class="d-inline-block mr-2">
                 @csrf @method('PATCH')
                <button type="submit" class="btn btn-sm btn-outline-secondary"><x-lucide-mail-open class="icon-sm mr-1"/> Mark as Unread</button>
            </form>
            --}}
             <form action="{{ route('admin.contact-messages.destroy', $contactMessage->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this message?');">
                 @csrf
                 @method('DELETE')
                 <button type="submit" class="btn btn-danger">
                     <x-lucide-trash-2 class="icon-sm mr-1"/> Delete Message
                 </button>
            </form>
        </div>
    </div>
@endsection

@push('styles')
<style>
.message-content { font-size: 1.05em; line-height: 1.7; }
</style>
@endpush