@extends('layouts.admin')
@section('title', 'Add Supplier')
@section('content')
    <div class="content-header"><h1>Add New Supplier</h1></div>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('admin.suppliers.store') }}" class="admin-form" enctype="multipart/form-data">
            @include('admin.suppliers._form')
        </form>
    </div></div>
@endsection