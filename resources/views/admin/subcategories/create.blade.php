@extends('layouts.admin')
@section('title', 'Add New Sub Category - AAM Store')
@section('content')
    <div class="content-header">
        <h1>Add New Sub Category</h1>
    </div>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.subcategories.store') }}" class="admin-form" enctype="multipart/form-data">
                {{-- Pass categories to the form --}}
                @include('admin.subcategories._form', ['categories' => $categories])
            </form>
        </div>
    </div>
@endsection