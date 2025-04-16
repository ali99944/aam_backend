@extends('layouts.admin')
@section('title', 'Manage Timezones')

@push('styles')
<style>
.status-badge { font-size: 0.8em; padding: 3px 8px; border-radius: 5px; }
.status-badge-active { background-color: #d1e7dd; color: #0f5132; }
.status-badge-inactive { background-color: #e2e3e5; color: #41464b; }
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>Manage Timezones</h1>
        <div class="actions">
            {{-- You might not manually add timezones often, maybe fetch from a package or seed them? --}}
            <a href="{{ route('admin.timezones.create') }}" class="btn btn-primary">
                <x-lucide-plus class="icon-sm mr-2"/> Add New Timezone
            </a>
             {{-- <button type="button" class="btn btn-info">Import/Sync Timezones</button> --}}
        </div>
    </div>

    {{-- Filter/Search Form --}}
    <div class="card mb-4">
        <div class="card-body py-2">
             <form method="GET" action="{{ route('admin.timezones.index') }}" class="form-inline">
                 <div class="form-group mr-2">
                     <label for="search" class="mr-1 d-none d-sm-inline">Search:</label>
                    <input type="text" id="search" name="search" class="form-control form-control-sm" placeholder="Identifier, Offset, Abbr..." value="{{ request('search') }}">
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
                    <a href="{{ route('admin.timezones.index') }}" class="btn btn-link btn-sm ml-2">Clear</a>
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
                            <th>Identifier</th>
                            <th>Offset</th>
                            <th>GMT Offset (sec)</th>
                            <th>Abbreviation</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($timezones as $timezone)
                            <tr>
                                <td><strong>{{ $timezone->name }}</strong></td>
                                <td>{{ $timezone->offset ?? '-' }}</td>
                                <td>{{ $timezone->gmt_offset ?? '-' }}</td>
                                <td>{{ $timezone->abbreviation ?? '-' }}</td>
                                <td>
                                      @if($timezone->is_active)
                                        <span class="status-badge status-badge-active">Active</span>
                                    @else
                                        <span class="status-badge status-badge-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td class="actions">
                                    <a href="{{ route('admin.timezones.edit', $timezone->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <x-lucide-pencil />
                                    </a>
                                    <form action="{{ route('admin.timezones.destroy', $timezone->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure? Check if this timezone is linked to Countries first.');">
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
                                <td colspan="6" class="text-center py-4">No timezones found. Consider seeding or importing them.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($timezones->hasPages())
            <div class="card-footer">
                 {{ $timezones->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection