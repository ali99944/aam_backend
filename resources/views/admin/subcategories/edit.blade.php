@extends('layouts.admin')
@section('title', 'Edit Sub Category - AAM Store')
@section('content')
    <div class="content-header">
        <h1>Edit Sub Category: {{ $subCategory->name }}</h1>
    </div>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.subcategories.update', $subCategory->id) }}" class="admin-form" enctype="multipart/form-data">
                @method('PUT')
                 {{-- Pass subCategory and categories to the form --}}
                @include('admin.subcategories._form', ['subCategory' => $subCategory, 'categories' => $categories])
            </form>
        </div>
    </div>
@endsection