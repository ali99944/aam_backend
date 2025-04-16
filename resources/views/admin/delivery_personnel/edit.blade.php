@extends('layouts.admin')
@section('title', 'Edit Delivery Person')

@section('content')
    <div class="content-header">
        <h1>Edit Delivery Person: <span class="text-primary">{{ $deliveryPersonnel->name }}</span></h1>
    </div>

    <div class="card">
        <div class="card-body">
             <form method="POST" action="{{ route('admin.delivery-personnel.update', $deliveryPersonnel->id) }}" class="admin-form" enctype="multipart/form-data">
                @method('PUT')
                @include('admin.delivery_personnel._form', ['deliveryPersonnel' => $deliveryPersonnel, 'companies' => $companies])
            </form>
        </div>
    </div>
@endsection