@extends('layouts.admin')
@section('title', 'Add Delivery Person')

@section('content')
    <div class="content-header">
        <h1>Add New Delivery Person</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.delivery-personnel.store') }}" class="admin-form" enctype="multipart/form-data">
                @include('admin.delivery_personnel._form', ['companies' => $companies])
            </form>
        </div>
    </div>
@endsection