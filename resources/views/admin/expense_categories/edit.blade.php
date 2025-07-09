@extends('layouts.admin')
@section('title', 'Edit Expense Category')

@section('content')
    <div class="content-header">
        <h1>تعديل قسم مصروفات: <span class="text-primary">{{ $expenseCategory->name }}</span></h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.expense-categories.update', $expenseCategory->id) }}" class="admin-form">
                @method('PUT')
                @include('admin.expense_categories._form', ['expenseCategory' => $expenseCategory])
            </form>
        </div>
    </div>
@endsection