@extends('layouts.admin')
@section('title', 'Record New Expense')

@section('content')
    <div class="content-header">
        <h1>تسجيل مصروف جديد</h1>
    </div>

    <div class="card">
        <div class="card-body">
             {{-- Add enctype for file uploads --}}
            <form method="POST" action="{{ route('admin.expenses.store') }}" class="admin-form" enctype="multipart/form-data">
                @include('admin.expenses._form', ['categories' => $categories])
            </form>
        </div>
    </div>
@endsection