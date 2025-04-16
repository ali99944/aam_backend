<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request) {
        $query = Faq::with('category'); // Eager load category

        if ($request->filled('search')) {
             $searchTerm = '%' . $request->search . '%';
             $query->where(function ($q) use ($searchTerm) {
                 $q->where('question', 'like', $searchTerm)
                   ->orWhere('answer', 'like', $searchTerm); // Search answer too
             });
        }
        if ($request->filled('faq_category_id')) {
            if ($request->faq_category_id === 'uncategorized') {
                $query->whereNull('faq_category_id');
            } else {
                $query->where('faq_category_id', $request->faq_category_id);
            }
        }
         if ($request->filled('is_active') && $request->is_active != 'all') {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        $faqs = $query->orderBy('display_order')->orderBy('question')->paginate(20);
        $categories = FaqCategory::where('is_active', true)->orderBy('name')->pluck('name', 'id');

        return view('admin.faqs.index', compact('faqs', 'categories'));
    }

    public function create() {
         $categories = FaqCategory::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        return view('admin.faqs.create', compact('categories'));
    }

    public function store(Request $request) {
         $validated = $request->validate([
            'faq_category_id' => 'nullable|exists:faq_categories,id',
            'question' => 'required|string|max:1000',
            'answer' => 'required|string', // Consider max length if needed
            'display_order' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        $validated['is_active'] = $request->has('is_active');
         // Sanitize answer HTML if using WYSIWYG editor (use a library like Purifier)
        // $validated['answer'] = clean($request->input('answer'));

        Faq::create($validated);
        return redirect()->route('admin.faqs.index')->with('success', 'FAQ created successfully.');
    }

    public function edit(Faq $faq) {
        $categories = FaqCategory::where('is_active', true)->orderBy('name')->pluck('name', 'id');
         // Ensure current category is available if inactive
        if ($faq->faq_category_id && !$categories->has($faq->faq_category_id)) {
             $currentCategory = FaqCategory::find($faq->faq_category_id);
             if ($currentCategory) $categories->put($currentCategory->id, $currentCategory->name . ' (Inactive)');
        }
        return view('admin.faqs.edit', compact('faq', 'categories'));
    }

    public function update(Request $request, Faq $faq) {
         $validated = $request->validate([
            'faq_category_id' => 'nullable|exists:faq_categories,id',
            'question' => 'required|string|max:1000',
            'answer' => 'required|string',
            'display_order' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        $validated['is_active'] = $request->has('is_active');
         // Sanitize answer HTML
        // $validated['answer'] = clean($request->input('answer'));

        $faq->update($validated);
        return redirect()->route('admin.faqs.index')->with('success', 'FAQ updated successfully.');
    }

    public function destroy(Faq $faq) {
        $faq->delete();
        return redirect()->route('admin.faqs.index')->with('success', 'FAQ deleted successfully.');
    }
}