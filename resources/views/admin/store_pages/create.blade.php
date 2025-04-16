@extends('layouts.admin')
@section('title', 'Create Store Page')
@section('content')
    <div class="content-header"><h1>Create New Store Page</h1></div>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('admin.store-pages.store') }}" class="admin-form">
            @csrf
            <div class="form-group mb-3">
                <label for="name">Page Name <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <small class="text-muted">User-friendly name (e.g., "About Us", "Homepage").</small>
            </div>
             <div class="form-group mb-3">
                <label for="key">Page Key <span class="text-danger">*</span></label>
                <input type="text" id="key" name="key" class="form-control @error('key') is-invalid @enderror" value="{{ old('key') }}" required>
                @error('key') <div class="invalid-feedback">{{ $message }}</div> @enderror
                 <small class="text-muted">Unique identifier used in code/URLs (e.g., "about-us", "home"). Only letters, numbers, underscores, hyphens.</small>
            </div>
            <div class="form-actions mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary">Create Page</button>
                <a href="{{ route('admin.store-pages.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div></div>
@endsection