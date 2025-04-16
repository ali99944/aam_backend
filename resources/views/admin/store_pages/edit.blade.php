@extends('layouts.admin')
@section('title', 'Edit Store Page')

@push('styles')
<style>
    .sections-container .card { margin-bottom: 1rem; }
    .section-actions .btn { margin-left: 5px;}
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>Edit Store Page: <span class="text-primary">{{ $storePage->name }}</span></h1>
    </div>

    {{-- Page Details Form --}}
    <div class="card mb-4">
         <div class="card-header">Page Details</div>
         <div class="card-body">
            <form method="POST" action="{{ route('admin.store-pages.update', $storePage->id) }}" class="admin-form">
                @csrf
                @method('PUT')
                 <div class="form-group mb-3">
                    <label for="name">Page Name <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $storePage->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                 <div class="form-group mb-3">
                    <label for="key">Page Key <span class="text-danger">*</span></label>
                    <input type="text" id="key" name="key" class="form-control @error('key') is-invalid @enderror" value="{{ old('key', $storePage->key) }}" required>
                    @error('key') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <small class="text-muted">Unique identifier (letters, numbers, _, -).</small>
                </div>
                 <div class="form-actions mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary">Update Page Details</button>
                </div>
            </form>
        </div>
    </div>
@endsection

