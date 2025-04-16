@extends('layouts.admin')
@section('title', 'Edit Language - AAM Store')

@section('content')
    <div class="content-header">
        <h1>Edit Language: <span class="text-primary">{{ $language->name }}</span></h1>
    </div>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.languages.update', $language->id) }}" class="admin-form" enctype="multipart/form-data">
                @method('PUT')
                @include('admin.languages._form', ['language' => $language, 'directions' => $directions])
            </form>
        </div>
    </div>
@endsection