@extends('layouts.admin')
@section('title', 'Edit Country')

@section('content')
    <div class="content-header">
        <h1>تعديل الدولة: <span class="text-primary">{{ $country->name }}</span></h1>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('admin.countries.update', $country->id) }}" class="admin-form p-3" enctype="multipart/form-data">
            @method('PUT')
            @include('admin.countries._form', ['country' => $country, 'currencies' => $currencies, 'timezones' => $timezones])
        </form>
    </div>
@endsection