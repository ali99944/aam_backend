@extends('layouts.admin')

@section('title', 'Manage Products - AAM Store')

@push('styles')
<style>
.table-product-image-preview { height: 50px; width: 50px; object-fit: cover; border-radius: 3px; border: 1px solid #eee; }
.status-label { font-size: 0.8em; font-weight: 600; padding: 3px 8px; border-radius: 10px; }
.status-label-active { background-color: #d1e7dd; color: #0f5132; }
.status-label-out-of-stock { background-color: #f8d7da; color: #842029; }
.visibility-icon { vertical-align: middle; }
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>Manage Products</h1>
        <div class="actions">
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <x-lucide-plus class="icon-sm mr-2"/> Add New Product
            </a>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="card mb-4">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.products.index') }}" class="form-inline flex-wrap"> {{-- Added flex-wrap --}}
                {{-- Search --}}
                 <div class="form-group mr-2 mb-2">
                     <label for="search" class="mr-1 d-none d-md-inline">Search:</label>
                    <input type="text" id="search" name="search" class="form-control form-control-sm" placeholder="Name, SKU, Brand..." value="{{ request('search') }}">
                </div>
                 {{-- Sub Category --}}
                <div class="form-group mr-2 mb-2">
                    <label for="filter_subcat" class="mr-1">Sub Cat:</label>
                    <select id="filter_subcat" name="sub_category_id" class="form-control form-control-sm" style="max-width: 150px;">
                         <option value="">All Sub Cats</option>
                         @foreach($subCategories as $id => $name)
                            <option value="{{ $id }}" {{ request('sub_category_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                         @endforeach
                    </select>
                </div>
                {{-- Brand --}}
                <div class="form-group mr-2 mb-2">
                    <label for="filter_brand" class="mr-1">Brand:</label>
                    <select id="filter_brand" name="brand_id" class="form-control form-control-sm" style="max-width: 150px;">
                         <option value="">All Brands</option>
                         @foreach($brands as $id => $name)
                            <option value="{{ $id }}" {{ request('brand_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                         @endforeach
                    </select>
                </div>
                 {{-- Status --}}
                 <div class="form-group mr-2 mb-2">
                    <label for="filter_status" class="mr-1">Status:</label>
                    <select id="filter_status" name="status" class="form-control form-control-sm">
                         <option value="all">All Statuses</option>
                         @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                         @endforeach
                    </select>
                </div>
                 {{-- Visibility --}}
                 <div class="form-group mr-2 mb-2">
                    <label for="filter_public" class="mr-1">Public:</label>
                    <select id="filter_public" name="is_public" class="form-control form-control-sm">
                         <option value="all">All</option>
                         <option value="1" {{ request('is_public') === '1' ? 'selected' : '' }}>Yes</option>
                         <option value="0" {{ request('is_public') === '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                {{-- Submit --}}
                <div class="form-group mb-2">
                    <button type="submit" class="btn btn-secondary btn-sm">
                        <x-lucide-filter class="icon-sm mr-1"/> Filter
                    </button>
                    @if(request()->hasAny(['search', 'sub_category_id', 'brand_id', 'status', 'is_public']))
                        <a href="{{ route('admin.products.index') }}" class="btn btn-link btn-sm ml-1">Clear</a>
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
                            <th>Image</th>
                            <th>Name</th>
                            <th>Brand</th>
                            <th>Sub Cat</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Public</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td>
                                    <img src="{{ $product->main_image_url ?? asset('images/placeholder-product.png') }}" alt="{{ $product->name }}" class="table-product-image-preview">
                                </td>
                                <td>
                                    <strong>{{ $product->name }}</strong>
                                    <small class="d-block text-muted">SKU: {{ $product->sku_code ?? 'N/A' }}</small>
                                </td>
                                <td>{{ $product->brand->name ?? 'N/A' }}</td>
                                <td>{{ $product->subCategory->name ?? 'N/A' }}</td>
                                <td>{{ $product->formatted_sell_price }}</td>
                                <td>{{ $product->stock }}</td>
                                <td>
                                    <span class="status-label status-label-{{ str_replace('_','-',$product->status) }}">
                                        {{ $statuses[$product->status] ?? ucfirst($product->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($product->is_public)
                                        <x-lucide-eye class="text-success visibility-icon" title="Visible"/>
                                    @else
                                        <x-lucide-eye-off class="text-muted visibility-icon" title="Hidden"/>
                                    @endif
                                </td>
                                <td class="actions">
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <x-lucide-pencil />
                                    </a>
                                    {{-- Toggle Featured Button --}}
                                    <form action="{{ route('admin.products.toggle-featured', $product->id) }}" method="POST" class="d-inline-block" title="{{ $product->is_featured ? 'Remove from Featured' : 'Mark as Featured' }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $product->is_featured ? 'btn-warning' : 'btn-outline-secondary' }}">
                                            <x-lucide-star size="16" fill="{{ $product->is_featured ? 'currentColor' : 'none' }}" />
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this product? This will also delete associated images, specs, and addons.');">
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
                                <td colspan="9" class="text-center py-4">No products found matching your criteria.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
         @if ($products->hasPages())
            <div class="card-footer">
                 {{ $products->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection