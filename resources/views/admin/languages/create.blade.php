@extends('layouts.admin')
@section('title', 'Add New Language - AAM Store')

@section('content')
    <div class="content-header">
        <h1>Add New Language</h1>
    </div>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.languages.store') }}" class="admin-form" enctype="multipart/form-data">
                @include('admin.languages._form', ['directions' => $directions])
            </form>
        </div>
    </div>
@endsection