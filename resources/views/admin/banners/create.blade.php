@extends('layouts.admin')
@section('title', 'إضافة بانر جديد')

@section('content')
    <div class="content-header">
        <h1>إضافة بانر جديد</h1>
    </div>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.banners.store') }}" class="admin-form" enctype="multipart/form-data">
                @include('admin.banners._form')
            </form>
        </div>
    </div>
@endsection