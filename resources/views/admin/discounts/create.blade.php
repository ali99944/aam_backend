@extends('layouts.admin')
@section('title', 'Add New Discount - AAM Store')

@section('content')
    <div class="content-header">
        <h1>Add New Discount</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.discounts.store') }}" class="admin-form">
                @include('admin.discounts._form', [
                    'discountTypes' => $discountTypes,
                    'statuses' => $statuses,
                    'expirationTypes' => $expirationTypes
                ])
            </form>
        </div>
    </div>
@endsection