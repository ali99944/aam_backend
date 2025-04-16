@extends('layouts.admin')
@section('title', 'Add New Offer')

@section('content')
    <div class="content-header">
        <h1>Add New Offer / Promotion</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.offers.store') }}" class="admin-form" enctype="multipart/form-data">
                @include('admin.offers._form', [
                    'types' => $types,
                    'categories' => $categories,
                    'products' => $products,
                    'brands' => $brands,
                 ])
            </form>
        </div>
    </div>
@endsection