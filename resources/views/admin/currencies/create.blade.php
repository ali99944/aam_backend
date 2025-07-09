@extends('layouts.admin')
@section('title', 'Add Currency')
@section('content') <div class="content-header">
        <h1>اضافة عملة جديدة</h1>
    </div>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.locations.currencies.store') }}">@include('admin.currencies._form')</form>
        </div>
</div> @endsection
