@extends('layouts.admin')
@section('title', 'Preview Email Templates')

@section('content')
    <div class="content-header">
        <h1>Preview Email Templates</h1>
    </div>

    <div class="card">
        <div class="card-header">Available Templates</div>
        <div class="card-body">
            <p>Select a template below to preview it with sample data.</p>
             <div class="list-group">
                @foreach ($templates as $key => $name)
                    <a href="{{ route('admin.templates.preview', $key) }}" class="list-group-item list-group-item-action" target="_blank">
                        <div>
                            {{ $name }} (<code>{{ $key }}</code>)
                        </div>
                        <div style="height: 20px; width: 20px;">
                            <x-lucide-external-link class="icon-sm float-end text-muted"/>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('styles')
{{-- Optional basic styling for list group if not in main CSS --}}
<style>
.list-group-item { display: flex; justify-content: space-between; align-items: center; }
.float-end { float: right !important; /* LTR */}
html[dir="rtl"] .float-end { float: left !important; }
html[dir="rtl"] .icon-sm.float-end { margin-right: auto; margin-left: 0;}
</style>
@endpush