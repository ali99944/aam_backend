{{-- resources/views/admin/store_page_sections/_section_card.blade.php --}}
<div class="card border">
    <div class="card-body d-flex justify-content-between align-items-center py-2 px-3">
        <div>
            <h5 class="card-title mb-0">{{ $section->name }}</h5>
            <small class="text-muted">Key: <code>{{ $section->key }}</code></small>
        </div>
        <div class="section-actions">
             {{-- Edit Button (links to separate edit view/modal) --}}
            <a href="{{ route('admin.store-pages.sections.edit', [$storePage->id, $section->id]) }}" class="btn btn-sm btn-outline-primary" title="Edit Section Content">
                <x-lucide-pencil /> Edit Content
            </a>
             {{-- Delete Form --}}
            <form action="{{ route('admin.store-pages.sections.destroy', [$storePage->id, $section->id]) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete the section \'{{ $section->name }}\'?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Section">
                    <x-lucide-trash-2 />
                </button>
            </form>
        </div>
    </div>
</div>