@extends('layouts.admin')
@section('title', 'Add Payment Method')
@section('content')
    <div class="content-header"><h1>Add New Payment Method</h1></div>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('admin.payment-methods.store') }}" class="admin-form" enctype="multipart/form-data">
            @include('admin.payment_methods._form')
        </form>
    </div></div>
@endsection