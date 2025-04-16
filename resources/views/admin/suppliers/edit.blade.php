@extends('layouts.admin')
@section('title', 'Edit Supplier')
@section('content')
    <div class="content-header"><h1>Edit Supplier: <span class="text-primary">{{ $supplier->name }}</span></h1></div>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('admin.suppliers.update', $supplier->id) }}" class="admin-form" enctype="multipart/form-data">
            @method('PUT')
            @include('admin.suppliers._form', ['supplier' => $supplier])
        </form>
    </div></div>
@endsection