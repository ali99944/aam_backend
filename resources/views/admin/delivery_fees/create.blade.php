@extends('layouts.admin')
@section('title', 'Add Delivery Fee')

@section('content')
    <div class="content-header">
        <h1>Add City-Specific Delivery Fee</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.delivery-fees.store') }}" class="admin-form">
                @include('admin.delivery_fees._form', ['availableCities' => $availableCities])
            </form>
        </div>
    </div>
@endsection