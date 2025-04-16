@extends('layouts.admin')
@section('title', 'Add Timezone')

@section('content')
    <div class="content-header">
        <h1>Add New Timezone</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.timezones.store') }}" class="admin-form">
                 {{-- Pass empty object to form if needed for old() helper on optional fields --}}
                 @php $timezone = new \App\Models\Timezone(); @endphp
                @include('admin.timezones._form', ['timezone' => $timezone])
            </form>
        </div>
    </div>
@endsection