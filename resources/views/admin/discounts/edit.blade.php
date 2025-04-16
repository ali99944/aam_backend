@extends('layouts.admin')
@section('title', 'Edit Discount - AAM Store')

@section('content')
    <div class="content-header">
        <h1>Edit Discount: <span class="text-primary">{{ $discount->name }}</span></h1>
         @if($discount->code)
            <span class="badge bg-secondary">Code: {{ $discount->code }}</span>
         @endif
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.discounts.update', $discount->id) }}" class="admin-form">
                @method('PUT')
                @include('admin.discounts._form', [
                    'discount' => $discount,
                    'discountTypes' => $discountTypes,
                    'statuses' => $statuses,
                    'expirationTypes' => $expirationTypes
                ])
            </form>
        </div>
    </div>
@endsection

{{-- Optional: Add badge styling if not in common.css --}}
@push('styles')
<style>.badge.bg-secondary { background-color: var(--secondary-color); color: white; }</style>
@endpush