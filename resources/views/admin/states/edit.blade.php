@extends('layouts.admin')
@section('title', 'Edit State/Province')

@section('content')
    <div class="content-header">
        <h1>Edit State/Province: <span class="text-primary">{{ $state->name }}</span></h1>
        <small class="text-muted d-block">Country: {{ $state->country->name ?? 'N/A' }}</small>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.states.update', $state->id) }}" class="admin-form">
                @method('PUT')
                @include('admin.states._form', ['state' => $state, 'countries' => $countries])
            </form>
        </div>
    </div>
@endsection