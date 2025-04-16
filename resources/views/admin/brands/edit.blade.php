@extends('layouts.admin')
@section('title', 'Edit Brand - AAM Store')

@section('content')
    <div class="content-header">
        <h1>Edit Brand: <span class="text-primary">{{ $brand->name }}</span></h1>
    </div>

    <div class="card">
        <div class="card-body">
             {{-- Add enctype for file uploads and method spoofing --}}
            <form method="POST" action="{{ route('admin.brands.update', $brand->id) }}" class="admin-form" enctype="multipart/form-data">
                @method('PUT') {{-- Specify PUT method for update --}}

                {{-- Include the form partial, passing the existing brand data --}}
                @include('admin.brands._form', ['brand' => $brand])
            </form>
        </div>
    </div>
@endsection