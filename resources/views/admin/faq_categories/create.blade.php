@extends('layouts.admin')
@section('title', 'Add FAQ Category')
@section('content')
    <div class="content-header"><h1>اضافة قسم اسئلة شائعة جديد</h1></div>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('admin.faq-categories.store') }}" class="admin-form">
            @include('admin.faq_categories._form')
        </form>
    </div></div>
@endsection