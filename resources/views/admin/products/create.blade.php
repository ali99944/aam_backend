@extends('layouts.admin')
@section('title', 'Add New Product - AAM Store')

@section('content')
    <div class="content-header">
        <h1>Add New Product</h1>
    </div>

     {{-- Add enctype for file uploads --}}
    <form method="POST" action="{{ route('admin.products.store') }}" class="admin-form" id="product-form" enctype="multipart/form-data">
        @include('admin.products._form', [
            'subCategories' => $subCategories,
            'brands' => $brands,
            'discounts' => $discounts,
            'statuses' => $statuses
        ])
    </form>
@endsection