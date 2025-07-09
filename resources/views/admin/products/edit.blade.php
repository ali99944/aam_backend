@extends('layouts.admin')
@section('title', 'Edit Product - AAM Store')

@section('content')
    <div class="content-header">
        <h1>تعديل المنتج: <span class="text-primary">{{ $product->name }}</span></h1>
        {{-- Show SKU --}}
        <span class="badge bg-light text-dark border">SKU: {{ $product->sku_code ?? 'N/A' }}</span>
    </div>

    <form method="POST" action="{{ route('admin.products.update', $product->id) }}" class="admin-form" id="product-form" enctype="multipart/form-data">
        @method('PUT')
        @include('admin.products._form', [
            'product' => $product, // Pass existing product
            'subCategories' => $subCategories,
            'brands' => $brands,
            'discounts' => $discounts,
            'statuses' => $statuses
        ])
    </form>
@endsection

@push('styles')
<style>.badge.bg-light { background-color: #f8f9fa!important; } .text-dark { color: #212529!important; } .border { border: 1px solid #dee2e6!important; }</style>
@endpush