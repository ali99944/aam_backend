@extends('layouts.admin')
@section('title', 'Page Details & Sections')
@section('content')
    <div class="content-header">
        <div>
            <h1>{{ $storePage->name }}</h1>
            <small class="text-muted">Key: <code>{{ $storePage->key }}</code></small>
        </div>
        <div class="actions">
            <a href="{{ route('admin.store-pages.edit', $storePage->id) }}" class="btn btn-outline-primary"><x-lucide-pencil class="icon-sm mr-1"/> Edit Page Details</a>
             <a href="{{ route('admin.store-pages.sections.create', $storePage->id) }}" class="btn btn-primary"><x-lucide-plus class="icon-sm mr-1"/> Add New Section</a>
            <a href="{{ route('admin.store-pages.index') }}" class="btn btn-secondary">Back to Pages</a>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header"><h3 class="card-title">Sections for this Page</h3></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="admin-table">
                     <thead><tr><th>Section Name</th><th>Section Key</th><th>Actions</th></tr></thead>
                     <tbody>
                        @forelse ($storePage->sections as $section)
                            <tr>
                                <td><strong>{{ $section->name }}</strong></td>
                                <td><code>{{ $section->key }}</code></td>
                                <td class="actions">
                                     <a href="{{ route('admin.store-pages.sections.edit', [$storePage->id, $section->id]) }}" class="btn btn-sm btn-outline-primary" title="Edit Section Content"><x-lucide-file-edit /></a>
                                     <form action="{{ route('admin.store-pages.sections.destroy', [$storePage->id, $section->id]) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete this section?');"> @csrf @method('DELETE') <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Section"><x-lucide-trash-2 /></button></form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-4">No sections added to this page yet.</td></tr>
                        @endforelse
                     </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection