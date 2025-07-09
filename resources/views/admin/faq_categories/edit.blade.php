@extends('layouts.admin')
@section('title', 'Edit FAQ Category')
@section('content')
    <div class="content-header"><h1>تعديل القسم: <span class="text-primary">{{ $faqCategory->name }}</span></h1></div>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('admin.faq-categories.update', $faqCategory->id) }}" class="admin-form">
            @method('PUT')
            @include('admin.faq_categories._form', ['faqCategory' => $faqCategory])
        </form>
    </div></div>
@endsection