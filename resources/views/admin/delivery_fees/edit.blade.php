@extends('layouts.admin')
@section('title', 'Edit Delivery Fee')

@section('content')
    <div class="content-header">
        <h1>Edit Delivery Fee for: <span class="text-primary">{{ $deliveryFee->city->name ?? 'N/A' }}</span></h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.delivery-fees.update', $deliveryFee->id) }}" class="admin-form">
                @method('PUT')
                @include('admin.delivery_fees._form', ['deliveryFee' => $deliveryFee])
            </form>
        </div>
    </div>
@endsection