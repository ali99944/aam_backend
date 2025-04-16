@extends('layouts.admin')
@section('title', 'Add Section to Page')

@section('content')
    <div class="content-header">
        <h1>Add New Section to: <span class="text-primary">{{ $storePage->name }}</span></h1>
         <a href="{{ route('admin.store-pages.show', $storePage->id) }}" class="btn btn-sm btn-secondary">Back to Page</a>
    </div>

    <div class="card">
        <div class="card-body">
            {{-- IMPORTANT: Add ID and enctype --}}
            <form id="section-form" method="POST" action="{{ route('admin.store-pages.sections.store', $storePage->id) }}" class="admin-form" enctype="multipart/form-data">
                @include('admin.store_page_sections._form', ['storePage' => $storePage])
            </form>
        </div>
    </div>
@endsection