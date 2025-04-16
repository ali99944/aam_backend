@extends('layouts.admin')
@section('title', 'Manage Countries')

@push('styles')
<style>
.table-flag-preview { height: 20px; width: auto; max-width: 35px; vertical-align: middle; border: 1px solid #eee; }
.status-badge { font-size: 0.8em; padding: 3px 8px; border-radius: 5px; }
.status-badge-active { background-color: #d1e7dd; color: #0f5132; }
.status-badge-inactive { background-color: #e2e3e5; color: #41464b; }
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>Manage Countries</h1>
        <div class="actions">
            <a href="{{ route('admin.countries.create') }}" class="btn btn-primary">
                <x-lucide-plus class="icon-sm mr-2"/> Add New Country
            </a>
        </div>
    </div>

    {{-- Filter/Search Form --}}
    <div class="card mb-4">
        <div class="card-body py-2">
             <form method="GET" action="{{ route('admin.countries.index') }}" class="form-inline">
                 <div class="form-group mr-2">
                     <label for="search" class="mr-1 d-none d-sm-inline">Search:</label>
                    <input type="text" id="search" name="search" class="form-control form-control-sm" placeholder="Name, Code, Capital..." value="{{ request('search') }}">
                </div>
                 <div class="form-group mr-2">
                    <label for="filter_active" class="mr-1">Status:</label>
                    <select id="filter_active" name="is_active" class="form-control form-control-sm">
                         <option value="all">All</option>
                         <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                         <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-secondary btn-sm">
                    <x-lucide-filter class="icon-sm mr-1"/> Filter
                </button>
                 @if(request('search') || request('is_active') != 'all')
                    <a href="{{ route('admin.countries.index') }}" class="btn btn-link btn-sm ml-2">Clear</a>
                 @endif
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width:5%">Flag</th>
                            <th>Name</th>
                            <th>ISO2</th>
                            <th>Phone Code</th>
                            <th>Currency</th>
                            <th>Timezone</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($countries as $country)
                            <tr>
                                <td class="text-center">
                                    <img src="{{ $country->flag_image_url }}" alt="{{ $country->name }} Flag" class="table-flag-preview">
                                </td>
                                <td><strong>{{ $country->name }}</strong></td>
                                <td>{{ $country->iso2 }}</td>
                                <td>{{ $country->phone_code ?? '-' }}</td>
                                <td>{{ $country->currency->code ?? 'N/A' }}</td> {{-- Assuming Currency has 'code' --}}
                                <td>{{ $country->timezone->name ?? 'N/A' }}</td> {{-- Display Timezone name --}}
                                <td>
                                      @if($country->is_active)
                                        <span class="status-badge status-badge-active">Active</span>
                                    @else
                                        <span class="status-badge status-badge-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td class="actions">
                                    <a href="{{ route('admin.countries.edit', $country->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <x-lucide-pencil />
                                    </a>
                                    <form action="{{ route('admin.countries.destroy', $country->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure? Check for related Cities/Data first.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <x-lucide-trash-2 />
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">No countries found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($countries->hasPages())
            <div class="card-footer">
                 {{ $countries->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection