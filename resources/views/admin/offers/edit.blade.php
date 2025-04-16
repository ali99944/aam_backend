@extends('layouts.admin')
@section('title', 'Edit Offer')

@section('content')
    <div class="content-header">
        <h1>Edit Offer: <span class="text-primary">{{ $offer->title }}</span></h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.offers.update', $offer->id) }}" class="admin-form" enctype="multipart/form-data">
                @method('PUT')
                 @include('admin.offers._form', [
                    'offer' => $offer,
                    'types' => $types,
                    'categories' => $categories,
                    'products' => $products,
                    'brands' => $brands,
                 ])
            </form>
        </div>
    </div>
@endsection