@extends('layouts.admin')
@section('title', 'Manage Store Pages')
@section('content')
    <div class="content-header">
        <h1>Manage Store Pages</h1>
        <div class="actions"><a href="{{ route('admin.store-pages.create') }}" class="btn btn-primary"><x-lucide-plus class="icon-sm mr-2"/> Add New Page</a></div>
    </div>
    {{-- Search Form --}}
    <div class="card mb-4"><div class="card-body py-2"> <form>...</form> </div></div>
    <div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="admin-table">
        <thead><tr><th>Name</th><th>Key</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse ($storePages as $page)
                <tr>
                    <td><strong>{{ $page->name }}</strong></td>
                    <td><code>{{ $page->key }}</code></td>
                    <td class="actions">
                         <a href="{{ route('admin.store-pages.show', $page->id) }}" class="btn btn-sm btn-outline-info" title="View Sections"><x-lucide-list /></a>
                         <a href="{{ route('admin.store-pages.edit', $page->id) }}" class="btn btn-sm btn-outline-primary" title="Edit Page"><x-lucide-pencil /></a>
                         <form action="{{ route('admin.store-pages.destroy', $page->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete page and ALL its sections?');"> @csrf @method('DELETE') <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Page"><x-lucide-trash-2 /></button></form>
                    </td>
                </tr>
            @empty <tr><td colspan="3" class="text-center py-4">No store pages found.</td></tr> @endforelse
        </tbody>
    </table></div></div>
     @if ($storePages->hasPages()) <div class="card-footer">{{ $storePages->links() }}</div> @endif
</div>
@endsection