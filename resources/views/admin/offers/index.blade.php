@extends('layouts.admin')
@section('title', 'Manage Offers')

@push('styles')
<style>
.table-offer-image-preview { max-height: 50px; max-width: 150px; object-fit: contain; vertical-align: middle; border-radius: 3px; }
.status-badge { font-size: 0.8em; padding: 3px 8px; border-radius: 5px; }
.status-badge-active { background-color: #d1e7dd; color: #0f5132; }
.status-badge-inactive { background-color: #e2e3e5; color: #41464b; }
.offer-link-info { font-size: 0.85em; color: #6c757d; }
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>Manage Offers & Promotions</h1>
        <div class="actions">
            <a href="{{ route('admin.offers.create') }}" class="btn btn-primary">
                <x-lucide-plus class="icon-sm mr-2"/> Add New Offer
            </a>
        </div>
    </div>

     {{-- Filter/Search Form --}}
    <div class="card mb-4">
        <div class="card-body py-2">
             <form method="GET" action="{{ route('admin.offers.index') }}" class="form-inline flex-wrap">
                 <div class="form-group mr-2 mb-2">
                     <label for="search" class="mr-1 d-none d-sm-inline">Search Title:</label>
                    <input type="text" id="search" name="search" class="form-control form-control-sm" placeholder="Enter title..." value="{{ request('search') }}">
                </div>
                 <div class="form-group mr-2 mb-2">
                    <label for="filter_type" class="mr-1">Type:</label>
                    <select id="filter_type" name="type" class="form-control form-control-sm">
                         <option value="all">All Types</option>
                         @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
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
                     @if(request()->hasAny(['search', 'type', 'is_active']))
                        <a href="{{ route('admin.offers.index') }}" class="btn btn-link btn-sm ml-1">Clear Filters</a>
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
                            <th style="width:15%">Image</th>
                            <th>Title</th>
                            <th>Type / Link</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Sort</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($offers as $offer)
                            <tr>
                                <td class="text-center">
                                    <img src="{{ $offer->image_url ?? asset('images/placeholder-offer.png') }}" alt="{{ $offer->title }}" class="table-offer-image-preview">
                                </td>
                                <td><strong>{{ $offer->title }}</strong></td>
                                <td class="offer-link-info">
                                    {{ App\Models\Offer::types()[$offer->type] ?? 'Unknown' }}
                                    @if($offer->type == App\Models\Offer::TYPE_GENERIC && $offer->target_url)
                                        <a href="{{ $offer->target_url }}" target="_blank" class="d-block" title="{{ $offer->target_url }}">
                                            <x-lucide-link class="icon-xs"/> {{ Str::limit($offer->target_url, 30) }}
                                        </a>
                                    @elseif ($offer->type == App\Models\Offer::TYPE_CATEGORY && $offer->linked_id)
                                         <span class="d-block">ID: {{ $offer->linked_id }} ({{ $offer->linked_category->name ?? 'N/A' }})</span>
                                    @elseif ($offer->type == App\Models\Offer::TYPE_PRODUCT && $offer->linked_id)
                                         <span class="d-block">ID: {{ $offer->linked_id }} ({{ Str::limit($offer->linked_product->name ?? 'N/A', 25) }})</span>
                                    @elseif ($offer->type == App\Models\Offer::TYPE_BRAND && $offer->linked_id)
                                        <span class="d-block">ID: {{ $offer->linked_id }} ({{ $offer->linked_brand->name ?? 'N/A' }})</span>
                                    @endif
                                </td>
                                <td>
                                    @if($offer->start_date || $offer->end_date)
                                        {{ $offer->start_date?->format('d M Y') ?? '...' }} - {{ $offer->end_date?->format('d M Y') ?? '...' }}
                                    @else
                                        <span class="text-muted">Always Active</span>
                                    @endif
                                </td>
                                <td>
                                      @if($offer->is_active)
                                        <span class="status-badge status-badge-active">Active</span>
                                    @else
                                        <span class="status-badge status-badge-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $offer->sort_order }}</td>
                                <td class="actions">
                                    <a href="{{ route('admin.offers.edit', $offer->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <x-lucide-pencil />
                                    </a>
                                    <form action="{{ route('admin.offers.destroy', $offer->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure?');">
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
                                <td colspan="7" class="text-center py-4">No offers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($offers->hasPages())
            <div class="card-footer">
                 {{ $offers->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection

{{-- Select2 includes if needed --}}
@push('styles') @endpush
@push('scripts') @endpush