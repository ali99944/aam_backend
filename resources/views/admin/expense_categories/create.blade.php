@extends('layouts.admin')
@section('title', 'Add Expense Category')

@section('content')
    <div class="content-header">
        <h1>اضافة قسم مصروفات جديد</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.expense-categories.store') }}" class="admin-form">
                @include('admin.expense_categories._form')
            </form>
        </div>
    </div>
@endsection