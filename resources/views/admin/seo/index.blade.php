@extends('layouts.admin')

@section('title', 'Manage Page SEO - AAM Store')

@section('content')
    <div class="content-header">
        <h1>Manage Page SEO Settings</h1>
        <div class="actions">
            <a href="{{ route('admin.seo.create') }}" class="btn btn-primary">
                <x-lucide-plus class="icon-sm mr-2"/> Add New Page SEO
            </a>
        </div>
    </div>

    {{-- Search Form --}}
    <div class="card mb-4">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.seo.index') }}" class="form-inline">
                <div class="form-group mr-2">
                     <label for="search" class="mr-1 d-none d-sm-inline">Search:</label>
                    <input type="text" id="search" name="search" class="form-control form-control-sm" placeholder="Search Name, Key, Title..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-secondary btn-sm">
                    <x-lucide-search class="icon-sm mr-1"/> Search
                </button>
                 @if(request('search'))
                    <a href="{{ route('admin.seo.index') }}" class="btn btn-link btn-sm ml-2">Clear</a>
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
                            <th>Admin Name</th>
                            <th>Page Key</th>
                            <th>Meta Title</th>
                            <th>Meta Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($seoPages as $seo)
                            <tr>
                                <td><strong>{{ $seo->name }}</strong></td>
                                <td><code>{{ $seo->key }}</code></td>
                                <td>{{ Str::limit($seo->title, 50) }}</td>
                                <td>{{ Str::limit($seo->description, 70) }}</td>
                                <td class="actions">
                                    <a href="{{ route('admin.seo.edit', $seo->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <x-lucide-pencil />
                                    </a>
                                    <form action="{{ route('admin.seo.destroy', $seo->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete SEO settings for page key \'{{ $seo->key }}\'?');">
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
                                <td colspan="5" class="text-center py-4">No page SEO settings found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
         @if ($seoPages->hasPages())
            <div class="card-footer">
                 {{ $seoPages->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection