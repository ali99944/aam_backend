@extends('layouts.admin')
@section('title', 'Add Country')

@section('content')
    <div class="content-header">
        <h1>Add New Country</h1>
    </div>

    <div class="card">
         {{-- No separate card body needed if form has cards --}}
        <form method="POST" action="{{ route('admin.countries.store') }}" class="admin-form p-3" enctype="multipart/form-data">
            @include('admin.countries._form', ['currencies' => $currencies, 'timezones' => $timezones])
        </form>
    </div>
@endsection