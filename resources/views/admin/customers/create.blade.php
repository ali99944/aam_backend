@extends('layouts.admin')
@section('title', 'إضافة عميل جديد')

@section('content')
    <div class="content-header">
        <h1>إضافة عميل جديد</h1>
        <div class="actions">
             <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
                 <x-lucide-arrow-right class="icon-sm ms-1"/> العودة إلى قائمة العملاء
             </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.customers.store') }}" class="admin-form">
        @include('admin.customers._form', ['statuses' => $statuses])
    </form>
@endsection