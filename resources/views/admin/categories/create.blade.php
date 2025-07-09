@extends('layouts.admin')
@section('title', 'Add New Category - AAM Store')
@section('content')
    <div class="content-header">
        <h1>اضافة قسم جديد</h1>
    </div>
    <div class="card">
        <div class="card-body">
            {{-- Add enctype here --}}
            <form method="POST" action="{{ route('admin.categories.store') }}" class="admin-form" enctype="multipart/form-data">
                @include('admin.categories._form')
            </form>
        </div>
    </div>
@endsection