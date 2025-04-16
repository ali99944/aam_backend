@extends('layouts.admin')
@section('title', 'Manage Languages - AAM Store')

@push('styles')
<style> .lang-flag { height: 20px; vertical-align: middle; margin-right: 5px; border: 1px solid #eee; } </style>
@endpush

@section('content')
    <div class="content-header">
        <h1>Manage Languages</h1>
        <div class="actions">
            <a href="{{ route('admin.languages.create') }}" class="btn btn-primary">
                <x-lucide-plus class="icon-sm mr-2"/> Add New Language
            </a>
        </div>
    </div>

    {{-- Optional Search Form --}}
     <div class="card mb-4">
        <div class="card-body py-2">
             <form method="GET" action="{{ route('admin.languages.index') }}" class="form-inline">
                 <div class="form-group mr-2 mb-0">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name, locale..." value="{{ request('search') }}">
                 </div>
                 <button type="submit" class="btn btn-secondary btn-sm"><x-lucide-search class="icon-sm mr-1"/> Search</button>
                 @if(request('search'))
                     <a href="{{ route('admin.languages.index') }}" class="btn btn-link btn-sm ml-1">Clear</a>
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
                            <th>Flag</th>
                            <th>Name</th>
                            <th>Native Name</th>
                            <th>Locale</th>
                            <th>Direction</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($languages as $language)
                            <tr>
                                <td>
                                    @if($language->flag_svg_url)
                                    <img src="{{ $language->flag_svg_url }}" alt="{{ $language->locale }}" class="lang-flag">
                                    @endif
                                </td>
                                <td>{{ $language->name }}</td>
                                <td>{{ $language->name_native }}</td>
                                <td><code>{{ $language->locale }}</code></td>
                                <td>{{ strtoupper($language->direction) }}</td>
                                <td>
                                     @if ($language->is_active)
                                        <span class="badge status-approved">Active</span>
                                    @else
                                        <span class="badge status-pending">Inactive</span>
                                    @endif
                                </td>
                                <td class="actions">
                                    <a href="{{ route('admin.languages.edit', $language->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <x-lucide-pencil />
                                    </a>
                                    {{-- Prevent deleting default/fallback locale? Add check in controller --}}
                                    <form action="{{ route('admin.languages.destroy', $language->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('WARNING: Deleting this language will remove ALL associated translations for ALL items. Are you absolutely sure?');">
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
                                <td colspan="7" class="text-center py-4">No languages found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
         @if ($languages->hasPages())
            <div class="card-footer">
                 {{ $languages->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection