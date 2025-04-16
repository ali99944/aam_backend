@extends('layouts.admin')
@section('title', 'Edit FAQ')
@section('content')
    <div class="content-header"><h1>Edit FAQ</h1></div>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('admin.faqs.update', $faq->id) }}" class="admin-form">
            @method('PUT')
            @include('admin.faqs._form', ['faq' => $faq, 'categories' => $categories])
        </form>
    </div></div>
@endsection