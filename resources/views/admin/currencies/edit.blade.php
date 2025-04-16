@extends('layouts.admin')
@section('title', 'Edit Currency')
@section('content') <div class="content-header">
        <h1>Edit Currency: {{ $currency->name }}</h1>
    </div>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.locations.currencies.update', $currency->id) }}">@method('PUT')
                @include('admin.currencies._form', ['currency' => $currency])</form>
        </div>
</div> @endsection
