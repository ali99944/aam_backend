@extends('layouts.admin')
@section('title', 'تعديل البانر')

@section('content')
    <div class="content-header">
        <h1>تعديل البانر: <span class="text-primary">{{ $banner->title }}</span></h1>
    </div>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.banners.update', $banner->id) }}" class="admin-form" enctype="multipart/form-data">
                @method('PUT')
                @include('admin.banners._form', ['banner' => $banner])
            </form>
        </div>
    </div>
@endsection