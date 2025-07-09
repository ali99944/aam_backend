@extends('layouts.admin')
@section('title', 'Edit Page SEO Settings - AAM Store')

@section('content')
    <div class="content-header">
        <h1>تعديل محركات البحث ل: <span class="text-primary">{{ $seo->name }}</span></h1>
        <span class="badge bg-light text-dark border">كود الصفحة: {{ $seo->key }}</span>
    </div>

     <form method="POST" action="{{ route('admin.seo.update', $seo->id) }}" class="admin-form" enctype="multipart/form-data">
        @method('PUT')
        @include('admin.seo._form', ['seo' => $seo])
    </form>
@endsection

{{-- Copied badge styles from product edit --}}
@push('styles')
<style>.badge.bg-light { background-color: #f8f9fa!important; } .text-dark { color: #212529!important; } .border { border: 1px solid #dee2e6!important; }</style>
@endpush