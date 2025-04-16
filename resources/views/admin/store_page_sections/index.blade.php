@extends('layouts.admin')
@section('title', 'Manage Sections for ' . $storePage->name)

@section('content')
    <div class="content-header">
        {{-- Breadcrumb or Title --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.store-pages.index') }}">Store Pages</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $storePage->name }} Sections</li>
            </ol>
        </nav>
        <h1>Manage Sections: <span class="text-primary">{{ $storePage->name }}</span></h1>

        <div class="actions">
            <a href="{{ route('admin.store-pages.sections.create', $storePage->id) }}" class="btn btn-primary">
                <x-lucide-plus class="icon-sm mr-2"/> Add New Section
            </a>
            <a href="{{ route('admin.store-pages.edit', $storePage->id) }}" class="btn btn-outline-secondary">Edit Page Details</a>
        </div>
    </div>

     <div class="card">
         <div class="card-body p-0"><div class="table-responsive"><table class="admin-table">
            <thead><tr><th>Section Name</th><th>Key</th><th>Content Preview</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse ($sections as $section)
                    <tr>
                        <td><strong>{{ $section->name }}</strong></td>
                        <td><code>{{ $section->key }}</code></td>
                        <td>
                            <small class="text-muted">
                                {{-- Show count or snippet of content --}}
                                {{ count($section->content ?? []) }} items defined.
                                {{-- Example snippet: --}}
                                {{-- json_encode(collect($section->content ?? [])->pluck('value', 'key')->take(2)->all()) --}}
                            </small>
                        </td>
                        <td class="actions">
                            <a href="{{ route('admin.store-pages.sections.edit', [$storePage->id, $section->id]) }}" class="btn btn-sm btn-outline-primary" title="Edit Section"><x-lucide-pencil /></a>
                            <form action="{{ route('admin.store-pages.sections.destroy', [$storePage->id, $section->id]) }}" method="POST" class="d-inline-block" onsubmit="return confirm('...');"> @csrf @method('DELETE') <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Section"><x-lucide-trash-2 /></button></form>
                        </td>
                    </tr>
                @empty <tr><td colspan="4" class="text-center py-4">No sections created for this page yet.</td></tr> @endforelse
            </tbody>
         </table></div></div>
     </div>
@endsection