@extends('layouts.admin')
@section('title', 'إضافة تقييم جديد')
@section('content')
    <div class="content-header"><h1>إضافة تقييم جديد</h1></div>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.testimonials.store') }}" class="admin-form" enctype="multipart/form-data">
                @include('admin.testimonials._form')
            </form>
        </div>
    </div>
@endsection