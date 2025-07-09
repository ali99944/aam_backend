@extends('layouts.admin')
@section('title', "تعديل بيانات العميل: {$customer->name}")

@section('content')
    <div class="content-header">
        <h1>تعديل بيانات العميل: <span class="text-primary">{{ $customer->name }}</span></h1>
         <div class="actions">
             <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
                 <x-lucide-arrow-right class="icon-sm ms-1"/> العودة إلى قائمة العملاء
             </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.customers.update', $customer->id) }}" class="admin-form">
        @method('PUT')
        @include('admin.customers._form', ['customer' => $customer, 'statuses' => $statuses])
    </form>
@endsection