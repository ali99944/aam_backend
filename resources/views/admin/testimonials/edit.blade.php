@extends('layouts.admin')
@section('title', 'تعديل التقييم')
@section('content')
    <div class="content-header"><h1>تعديل تقييم: <span class="text-primary">{{ $testimonial->name }}</span></h1></div>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.testimonials.update', $testimonial->id) }}" class="admin-form" enctype="multipart/form-data">
                @method('PUT')
                @include('admin.testimonials._form', ['testimonial' => $testimonial])
            </form>
        </div>
    </div>
@endsection