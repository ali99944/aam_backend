@extends('layouts.admin')
@section('title', 'Manage States/Provinces')

@push('styles')
<style>
.status-badge { font-size: 0.8em; padding: 3px 8px; border-radius: 5px; }
.status-badge-active { background-color: #d1e7dd; color: #0f5132; }
.status-badge-inactive { background-color: #e2e3e5; color: #41464b; }
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>Manage States/Provinces/Governorates</h1>
        <div class="actions">
            <a href="{{ route('admin.states.create') }}" class="btn btn-primary">
                <x-lucide-plus class="icon-sm mr-2"/> Add New State
            </a>
        </div>
    </div>

    {{-- Filter/Search Form --}}
    <div class="card mb-4">
        <div class="card-body py-2">
             <form method="GET" action="{{ route('admin.states.index') }}" class="form-inline flex-wrap">
                 <div class="form-group mr-2 mb-2">
                     <label for="search" class="mr-1 d-none d-sm-inline">Search:</label>
                    <input type="text" id="search" name="search" class="form-control form-control-sm" placeholder="State, Code, Country..." value="{{ request('search') }}">
                </div>
                 <div class="form-group mr-2 mb-2">
                    <label for="filter_country" class="mr-1">Country:</label>
                    <select id="filter_country" name="country_id" class="form-control form-control-sm select2"> {{-- Consider Select2 --}}
                         <option value="">All Countries</option>
                         @foreach($countries as $id => $name)
                            <option value="{{ $id }}" {{ request('country_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                         @endforeach
                    </select>
                </div>
                 <div class="form-group mr-2 mb-2">
                    <label for="filter_active" class="mr-1">Status:</label>
                    <select id="filter_active" name="is_active" class="form-control form-control-sm">
                         <option value="all">All</option>
                         <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                         <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="form-group mb-2">
                    <button type="submit" class="btn btn-secondary btn-sm">
                        <x-lucide-filter class="icon-sm mr-1"/> Filter
                    </button>
                     @if(request()->hasAny(['search', 'country_id', 'is_active']))
                        <a href="{{ route('admin.states.index') }}" class="btn btn-link btn-sm ml-1">Clear Filters</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>State Name</th>
                            <th>Code</th>
                            <th>Country</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($states as $state)
                            <tr>
                                <td><strong>{{ $state->name }}</strong></td>
                                <td>{{ $state->state_code ?? '-' }}</td>
                                <td>{{ $state->country->name ?? 'N/A' }}</td>
                                <td>
                                      @if($state->is_active)
                                        <span class="status-badge status-badge-active">Active</span>
                                    @else
                                        <span class="status-badge status-badge-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td class="actions">
                                    <a href="{{ route('admin.states.edit', $state->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <x-lucide-pencil />
                                    </a>
                                    <form action="{{ route('admin.states.destroy', $state->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure? Check for related Cities first.');">
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
                                <td colspan="5" class="text-center py-4">No states found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($states->hasPages())
            <div class="card-footer">
                 {{ $states->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection

{{-- Include Select2 if needed --}}
@push('styles') @endpush
@push('scripts') @endpush