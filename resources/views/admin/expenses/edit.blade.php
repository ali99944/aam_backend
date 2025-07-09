@extends('layouts.admin')
@section('title', 'Edit Expense')

@section('content')
    <div class="content-header">
        <h1>تعديل مصروف</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.expenses.update', $expense->id) }}" class="admin-form" enctype="multipart/form-data">
                @method('PUT')
                @include('admin.expenses._form', ['expense' => $expense, 'categories' => $categories])
            </form>
        </div>
    </div>
@endsection