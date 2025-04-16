{{-- resources/views/admin/expenses/_form.blade.php --}}
@csrf

<div class="row">
    <div class="col-md-6">
         {{-- Category --}}
        <div class="form-group mb-3">
            <label for="expense_category_id">Expense Category <span class="text-danger">*</span></label>
            <select id="expense_category_id" name="expense_category_id" class="form-control @error('expense_category_id') is-invalid @enderror" required>
                <option value="">-- Select Category --</option>
                @foreach($categories as $id => $name)
                    <option value="{{ $id }}" {{ old('expense_category_id', $expense->expense_category_id ?? '') == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            @error('expense_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
     <div class="col-md-6">
         {{-- Entry Date --}}
        <div class="form-group mb-3">
            <label for="entry_date">Expense Date <span class="text-danger">*</span></label>
            <input type="date" id="entry_date" name="entry_date" class="form-control @error('entry_date') is-invalid @enderror"
                   value="{{ old('entry_date', isset($expense->entry_date) ? $expense->entry_date->format('Y-m-d') : date('Y-m-d')) }}" required>
            @error('entry_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
         {{-- Amount --}}
        <div class="form-group mb-3">
            <label for="amount">Amount (AED) <span class="text-danger">*</span></label>
            <input type="number" id="amount" name="amount" class="form-control @error('amount') is-invalid @enderror"
                   value="{{ old('amount', $expense->amount ?? '') }}" required step="0.01" min="0.01">
             @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
    <div class="col-md-6">
        {{-- Receipt Image --}}
        <div class="form-group mb-3">
            <label for="receipt_image">Receipt (Optional)</label>
            <input type="file" id="receipt_image" name="receipt_image" class="form-control @error('receipt_image') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,application/pdf">
            <small class="form-text text-muted">Accepted: JPG, PNG, PDF. Max 2MB.</small>
            @error('receipt_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
             @if(isset($expense) && $expense->receipt_image_url)
                <div class="mt-2">
                    <p class="mb-1"><small>Current Receipt:</small></p>
                    @if (Str::endsWith($expense->receipt_image, '.pdf'))
                         <a href="{{ $expense->receipt_image_url }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                            <x-lucide-file-text class="icon-sm mr-1"/> View PDF Receipt
                        </a>
                    @else
                         <a href="{{ $expense->receipt_image_url }}" target="_blank">
                            <img src="{{ $expense->receipt_image_url }}" alt="Receipt" style="max-height: 100px;" class="img-thumbnail">
                         </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>


{{-- Description --}}
<div class="form-group mb-3">
    <label for="description">Description / Notes <span class="text-danger">*</span></label>
    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
              rows="4" required>{{ old('description', $expense->description ?? '') }}</textarea>
    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>


<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <x-lucide-save class="icon-sm mr-1"/> {{ isset($expense) ? 'Update Expense' : 'Record Expense' }}
    </button>
    <a href="{{ route('admin.expenses.index') }}" class="btn btn-secondary">Cancel</a>
</div>