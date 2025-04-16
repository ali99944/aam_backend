@extends('layouts.admin')
@section('title', 'Edit Timezone')

@section('content')
    <div class="content-header">
        <h1>Edit Timezone: <span class="text-primary">{{ $timezone->name }}</span></h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.timezones.update', $timezone->id) }}" class="admin-form">
                @method('PUT')
                @include('admin.timezones._form', ['timezone' => $timezone])
            </form>
        </div>
    </div>
@endsection