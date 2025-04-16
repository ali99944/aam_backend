<?php // Simplified version - Add validation etc. as needed
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request) {
        $query = ExpenseCategory::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $categories = $query->orderBy('name')->paginate(15);
        return view('admin.expense_categories.index', compact('categories'));
    }

    public function create() {
        return view('admin.expense_categories.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);
        $validated['is_active'] = $request->has('is_active');
        ExpenseCategory::create($validated);
        return redirect()->route('admin.expense-categories.index')->with('success', 'Category created.');
    }

    public function edit(ExpenseCategory $expenseCategory) {
        return view('admin.expense_categories.edit', compact('expenseCategory'));
    }

    public function update(Request $request, ExpenseCategory $expenseCategory) {
         $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name,' . $expenseCategory->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);
         $validated['is_active'] = $request->has('is_active');
        $expenseCategory->update($validated);
        return redirect()->route('admin.expense-categories.index')->with('success', 'Category updated.');
    }

    public function destroy(ExpenseCategory $expenseCategory) {
         // Check if category has expenses before deleting
        if ($expenseCategory->expenses()->exists()) {
            return back()->with('error', 'Cannot delete category with existing expenses.');
        }
        $expenseCategory->delete();
        return redirect()->route('admin.expense-categories.index')->with('success', 'Category deleted.');
    }
}