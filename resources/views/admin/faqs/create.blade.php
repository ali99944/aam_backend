@extends('layouts.admin')
@section('title', 'Add FAQ')
@section('content')
    <div class="content-header"><h1>اضافة سؤال شائع جديد</h1></div>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('admin.faqs.store') }}" class="admin-form">
            @include('admin.faqs._form', ['categories' => $categories])
        </form>
    </div></div>
@endsection