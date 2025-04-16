@extends('layouts.admin')
@section('title', 'Manage Currencies')
@section('content')
    <div class="content-header">
        <h1>Manage Currencies</h1>
        <a href="{{ route('admin.locations.currencies.create') }}" class="btn btn-primary"><x-lucide-plus/> Add New</a>
    </div>
     {{-- Filter Form --}}
    <div class="card mb-4"><div class="card-body py-2">
         <form method="GET" action="{{ route('admin.locations.currencies.index') }}" class="form-inline">
             <div class="form-group mr-2 mb-0">
                 <label for="is_active" class="mr-1">Status:</label>
                 <select name="is_active" id="is_active" class="form-control form-control-sm">
                     <option value="all">All</option>
                     <option value="1" {{ request('is_active')=='1'?'selected':''}}>Active</option>
                     <option value="0" {{ request('is_active')=='0'?'selected':''}}>Inactive</option>
                 </select>
             </div>
             <div class="form-group mr-2 mb-0">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Name or Code..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-secondary btn-sm"><x-lucide-filter class="icon-sm mr-1"/> Filter</button>
            @if(request('search') || request('is_active') != 'all')
             <a href="{{ route('admin.locations.currencies.index') }}" class="btn btn-link btn-sm ml-1">Clear</a>
            @endif
        </form>
    </div></div>
    <div class="card"><div class="card-body p-0"><div class="table-responsive">
        <table class="admin-table">
            <thead><tr><th>Name</th><th>Code</th><th>Symbol</th><th>Exchange Rate</th><th>Active</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse ($currencies as $currency)
                    <tr>
                        <td><strong>{{ $currency->name }}</strong></td>
                        <td>{{ $currency->code }}</td>
                        <td>{{ $currency->symbol }}</td>
                        <td>{{ rtrim(rtrim(number_format($currency->exchange_rate, 6), '0'), '.') }}</td>
                        <td>@if($currency->is_active)<x-lucide-check-circle class="text-success"/>@else<x-lucide-x-circle class="text-danger"/>@endif</td>
                        <td class="actions">
                            <a href="{{ route('admin.locations.currencies.edit', $currency->id) }}" class="btn btn-sm btn-outline-primary"><x-lucide-pencil/></a>
                            <form action="{{ route('admin.locations.currencies.destroy', $currency->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete {{ $currency->code }}?');">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger"><x-lucide-trash-2/></button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4">No currencies found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div></div>
    @if ($currencies->hasPages())<div class="card-footer">{{ $currencies->links() }}</div>@endif
    </div>
@endsection