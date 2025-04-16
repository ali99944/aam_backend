@extends('layouts.admin')
@section('title', 'Add State/Province')

@section('content')
    <div class="content-header">
        <h1>Add New State/Province/Governorate</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.states.store') }}" class="admin-form">
                @include('admin.states._form', ['countries' => $countries])
            </form>
        </div>
    </div>
@endsection