<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqCategory;
use Illuminate\Http\Request;

class FaqCategoryController extends Controller
{
    public function index(Request $request) {
        $query = FaqCategory::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $categories = $query->orderBy('display_order')->orderBy('name')->paginate(15);
        return view('admin.faq_categories.index', compact('categories'));
    }

    public function create() {
        return view('admin.faq_categories.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:faq_categories,name',
            'description' => 'nullable|string|max:1000',
            'display_order' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        $validated['is_active'] = $request->has('is_active');
        FaqCategory::create($validated);
        return redirect()->route('admin.faq-categories.index')->with('success', 'FAQ Category created.');
    }

    public function edit(FaqCategory $faqCategory) {
        return view('admin.faq_categories.edit', compact('faqCategory'));
    }

    public function update(Request $request, FaqCategory $faqCategory) {
         $validated = $request->validate([
            'name' => 'required|string|max:255|unique:faq_categories,name,' . $faqCategory->id,
            'description' => 'nullable|string|max:1000',
            'display_order' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
         $validated['is_active'] = $request->has('is_active');
        $faqCategory->update($validated);
        return redirect()->route('admin.faq-categories.index')->with('success', 'FAQ Category updated.');
    }

    public function destroy(FaqCategory $faqCategory) {
        // Optionally set FAQs in this category to null or prevent deletion
        // $faqCategory->faqs()->update(['faq_category_id' => null]); // Example: Unassign FAQs
        if ($faqCategory->faqs()->exists()) {
             return back()->with('error', 'Cannot delete category with assigned FAQs. Reassign or delete FAQs first.');
        }
        $faqCategory->delete();
        return redirect()->route('admin.faq-categories.index')->with('success', 'FAQ Category deleted.');
    }
}