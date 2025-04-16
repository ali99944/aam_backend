<?php // Simplified version - Add validation etc. as needed
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    private function handleImageUpload(Request $request, string $fieldName, string $directory, ?string $oldPath = null): ?string {
         if ($request->hasFile($fieldName)) {
            if ($oldPath) Storage::disk('public')->delete($oldPath);
            return $request->file($fieldName)->store($directory, 'public');
         }
         return $oldPath; // Keep old if no new file
    }
    private function deleteImage(?string $path): void { if ($path) Storage::disk('public')->delete($path); }

    public function index(Request $request) {
        $query = Expense::with('category');
         if ($request->filled('expense_category_id')) {
            $query->where('expense_category_id', $request->expense_category_id);
         }
         if ($request->filled('start_date')) {
            $query->whereDate('entry_date', '>=', $request->start_date);
         }
         if ($request->filled('end_date')) {
            $query->whereDate('entry_date', '<=', $request->end_date);
         }
        $expenses = $query->orderBy('entry_date', 'desc')->paginate(20);
        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        return view('admin.expenses.index', compact('expenses', 'categories'));
    }

    public function create() {
        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        return view('admin.expenses.create', compact('categories'));
    }

    public function store(Request $request) {
         $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'entry_date' => 'required|date',
            'description' => 'required|string|max:2000',
            'receipt_image' => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:2048', // Allow PDF for receipts
        ]);
         $validated['receipt_image'] = $this->handleImageUpload($request, 'receipt_image', 'expenses/receipts');

        Expense::create($validated);
        return redirect()->route('admin.expenses.index')->with('success', 'Expense recorded.');
    }

     public function edit(Expense $expense) {
        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        return view('admin.expenses.edit', compact('expense', 'categories'));
    }

     public function update(Request $request, Expense $expense) {
          $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'entry_date' => 'required|date',
            'description' => 'required|string|max:2000',
            'receipt_image' => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);
        $validated['receipt_image'] = $this->handleImageUpload($request, 'receipt_image', 'expenses/receipts', $expense->receipt_image);

        if (!$request->hasFile('receipt_image') && array_key_exists('receipt_image', $validated) && $validated['receipt_image'] === null) {
             unset($validated['receipt_image']); // Keep old image if no new file
        } elseif ($validated['receipt_image'] === null) {
            $validated['receipt_image'] = $expense->receipt_image;
        }


         $expense->update($validated);
         return redirect()->route('admin.expenses.index')->with('success', 'Expense updated.');
    }

    public function destroy(Expense $expense) {
        $this->deleteImage($expense->receipt_image);
        $expense->delete();
        return redirect()->route('admin.expenses.index')->with('success', 'Expense deleted.');
    }
}