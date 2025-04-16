@extends('layouts.admin')
@section('title', 'Add New Brand - AAM Store')

@section('content')
    <div class="content-header">
        <h1>Add New Brand</h1>
    </div>

    <div class="card">
        <div class="card-body">
            {{-- Add enctype for file uploads --}}
            <form method="POST" action="{{ route('admin.brands.store') }}" class="admin-form" enctype="multipart/form-data">
                {{-- Include the form partial --}}
                @include('admin.brands._form')
            </form>
        </div>
    </div>
@endsection