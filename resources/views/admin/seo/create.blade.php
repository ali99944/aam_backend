@extends('layouts.admin')
@section('title', 'Add New Page SEO Settings - AAM Store')

@section('content')
    <div class="content-header">
        <h1>Add New Page SEO Settings</h1>
    </div>

    <form method="POST" action="{{ route('admin.seo.store') }}" class="admin-form" enctype="multipart/form-data">
        @include('admin.seo._form')
    </form>
@endsection