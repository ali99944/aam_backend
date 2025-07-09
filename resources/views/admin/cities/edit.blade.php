@extends('layouts.admin')
@section('title', 'Edit City')

@section('content')
     <div class="content-header">
        <h1>تعديل المدينة: <span class="text-primary">{{ $city->name }}</span></h1>
        <small class="text-muted d-block">State: {{ $city->state->name ?? 'N/A' }} | Country: {{ $city->country->name ?? 'N/A' }}</small>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.cities.update', $city->id) }}" class="admin-form">
                @method('PUT')
                @include('admin.cities._form', ['city' => $city, 'statesGrouped' => $statesGrouped]) {{-- Pass grouped states --}}
            </form>
        </div>
    </div>
@endsection