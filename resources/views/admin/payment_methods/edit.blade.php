@extends('layouts.admin')
@section('title', 'Edit Payment Method')
@section('content')
    <div class="content-header"><h1>Edit Payment Method: <span class="text-primary">{{ $paymentMethod->name }}</span></h1></div>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('admin.payment-methods.update', $paymentMethod->id) }}" class="admin-form" enctype="multipart/form-data">
            @method('PUT')
            @include('admin.payment_methods._form', ['paymentMethod' => $paymentMethod])
        </form>
    </div></div>
@endsection