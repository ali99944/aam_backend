@extends('layouts.admin')
@section('title', 'Edit Section')

@section('content')
    <div class="content-header">
         <h1>Edit Section: <span class="text-primary">{{ $section->name }}</span></h1>
         <span class="text-muted"> (Parent Page: {{ $storePage->name }})</span>
         <a href="{{ route('admin.store-pages.show', $storePage->id) }}" class="btn btn-sm btn-secondary ms-3">Back to Page</a>
    </div>

    <div class="card">
        <div class="card-body">
             {{-- IMPORTANT: Add ID and enctype --}}
             <form id="section-form" method="POST" action="{{ route('admin.store-pages.sections.update', [$storePage->id, $section->id]) }}" class="admin-form" enctype="multipart/form-data">
                @method('PUT')
                @include('admin.store_page_sections._form', ['storePage' => $storePage, 'section' => $section])
            </form>
        </div>
    </div>
@endsection