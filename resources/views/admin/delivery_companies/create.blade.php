@extends('layouts.admin')
@section('title', 'Add Delivery Company')

@section('content')
    <div class="content-header">
        <h1>Add New Delivery Company</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.delivery-companies.store') }}" class="admin-form" enctype="multipart/form-data">
                @include('admin.delivery_companies._form')
            </form>
        </div>
    </div>
@endsection