@extends('layouts.admin')
@section('title', 'Manage Expenses')

@push('styles')
<style>
.receipt-link a { text-decoration: none; }
.receipt-link .lucide { vertical-align: middle; }
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>Manage Expenses</h1>
        <div class="actions">
            <a href="{{ route('admin.expenses.create') }}" class="btn btn-primary">
                <x-lucide-plus class="icon-sm mr-2"/> Record New Expense
            </a>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="card mb-4">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.expenses.index') }}" class="form-inline flex-wrap">
                 <div class="form-group mr-2 mb-2">
                    <label for="filter_category" class="mr-1">Category:</label>
                    <select id="filter_category" name="expense_category_id" class="form-control form-control-sm">
                         <option value="">All Categories</option>
                         @foreach($categories as $id => $name)
                            <option value="{{ $id }}" {{ request('expense_category_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                         @endforeach
                    </select>
                </div>
                <div class="form-group mr-2 mb-2">
                     <label for="start_date" class="mr-1">From:</label>
                     <input type="date" id="start_date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                </div>
                 <div class="form-group mr-2 mb-2">
                     <label for="end_date" class="mr-1">To:</label>
                     <input type="date" id="end_date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                </div>
                <div class="form-group mb-2">
                    <button type="submit" class="btn btn-secondary btn-sm">
                        <x-lucide-filter class="icon-sm mr-1"/> Filter
                    </button>
                     @if(request()->hasAny(['expense_category_id', 'start_date', 'end_date']))
                        <a href="{{ route('admin.expenses.index') }}" class="btn btn-link btn-sm ml-1">Clear Filters</a>
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
                            <th>Date</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Receipt</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($expenses as $expense)
                            <tr>
                                <td>{{ $expense->entry_date->format('d M Y') }}</td>
                                <td>{{ $expense->category->name ?? 'N/A' }}</td>
                                <td><strong>{{ $expense->formatted_amount }}</strong></td>
                                <td>{{ Str::limit($expense->description, 70) }}</td>
                                <td class="text-center receipt-link">
                                    @if($expense->receipt_image_url)
                                        <a href="{{ $expense->receipt_image_url }}" target="_blank" title="View Receipt">
                                            @if (Str::endsWith($expense->receipt_image, '.pdf'))
                                                <x-lucide-file-text class="text-danger"/>
                                            @else
                                                <x-lucide-image class="text-info"/>
                                            @endif
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="actions">
                                    <a href="{{ route('admin.expenses.edit', $expense->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <x-lucide-pencil />
                                    </a>
                                    <form action="{{ route('admin.expenses.destroy', $expense->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this expense record?');">
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
                                <td colspan="7" class="text-center py-4">No expenses found matching your criteria.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($expenses->hasPages())
            <div class="card-footer">
                {{-- Display total for current view? --}}
                {{-- <span class="float-end">Total: AED {{ number_format($expenses->sum('amount'), 2) }}</span> --}}
                 {{ $expenses->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection