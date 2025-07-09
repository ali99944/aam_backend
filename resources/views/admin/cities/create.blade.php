@extends('layouts.admin')
@section('title', 'Add City')

@section('content')
    <div class="content-header">
        <h1>اضافة مدينة جديدة</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.cities.store') }}" class="admin-form">
                @include('admin.cities._form', ['statesGrouped' => $statesGrouped, 'city' => $city]) {{-- Pass grouped states --}}
            </form>
        </div>
    </div>
@endsection