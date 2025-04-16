@extends('layouts.admin')
@section('title', 'Edit Delivery Company')

@section('content')
    <div class="content-header">
        <h1>Edit Delivery Company: <span class="text-primary">{{ $deliveryCompany->name }}</span></h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.delivery-companies.update', $deliveryCompany->id) }}" class="admin-form" enctype="multipart/form-data">
                @method('PUT')
                @include('admin.delivery_companies._form', ['deliveryCompany' => $deliveryCompany])
            </form>
        </div>
    </div>
@endsection