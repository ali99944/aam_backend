@extends('layouts.admin')
@section('title', 'Manage Policies - AAM Store')

@section('content')
    <div class="content-header">
        <h1>Manage Policies</h1>
        {{-- Policies are typically predefined, maybe disable 'Add New'? Or allow adding if needed. --}}
        <a href="{{ route('admin.policies.create') }}" class="btn btn-primary"><x-lucide-plus/> Add New</a>
    </div>

    {{-- Search Form --}}
    <div class="card mb-4">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.policies.index') }}" class="form-inline">
                <div class="form-group mr-2 mb-0">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search Name or Key..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-secondary btn-sm"><x-lucide-search class="icon-sm mr-1"/> Search</button>
                @if(request('search'))
                 <a href="{{ route('admin.policies.index') }}" class="btn btn-link btn-sm ml-1">Clear</a>
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
                            <th>Name</th>
                            <th>Key</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($policies as $policy)
                            <tr>
                                <td><strong>{{ $policy->name }}</strong></td>
                                <td><code>{{ $policy->key }}</code></td>
                                <td>{{ $policy->updated_at->format('d M Y, H:i') }}</td>
                                <td class="actions">
                                    {{-- View/Preview Button --}}
                                    <a href="{{ route('admin.policies.show', $policy->id) }}" class="btn btn-sm btn-outline-info" title="Preview" target="_blank"> {{-- Open in new tab --}}
                                        <x-lucide-eye />
                                    </a>
                                     {{-- Edit Button --}}
                                    <a href="{{ route('admin.policies.edit', $policy->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <x-lucide-pencil />
                                    </a>
                                    {{-- Delete Button (Use with caution) --}}
                                    <form action="{{ route('admin.policies.destroy', $policy->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete the \'{{ $policy->name }}\' policy? This action cannot be undone.');">
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
                                <td colspan="4" class="text-center py-4">No policies found. Seed initial policies if needed.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($policies->hasPages())
            <div class="card-footer">
                 {{ $policies->links() }}
            </div>
        @endif
    </div>
@endsection