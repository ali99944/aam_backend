@extends('layouts.admin')
@section('title', 'Edit Category - AAM Store')
@section('content')
    <div class="content-header">
        <h1>تعديل القسم الرئيسي: {{ $category->name }}</h1>
    </div>
    <div class="card">
        <div class="card-body">
             {{-- Add enctype here --}}
            <form method="POST" action="{{ route('admin.categories.update', $category->id) }}" class="admin-form" enctype="multipart/form-data">
                @method('PUT')
                @include('admin.categories._form', ['category' => $category])
            </form>
        </div>
    </div>
@endsection