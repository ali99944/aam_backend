<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StorePage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StorePageController extends Controller
{
    public function index(Request $request) {
        $query = StorePage::query();
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where('name', 'like', $searchTerm)->orWhere('key', 'like', $searchTerm);
        }
        $storePages = $query->orderBy('name')->paginate(15);
        return view('admin.store_pages.index', compact('storePages'));
    }

    public function create() {
        return view('admin.store_pages.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'key' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9_-]+$/', 'unique:store_pages,key'],
        ], [
            'key.regex' => 'The key can only contain lowercase letters, numbers, underscores, and hyphens.',
            'key.unique' => 'This key is already taken.',
        ]);

        $storePage = StorePage::create($validated);
        // Redirect to the show page after creation
        return redirect()->route('admin.store-pages.show', $storePage->id)
                         ->with('success', 'Store Page created successfully. You can now add sections.');
    }

    // Show page details and its sections
    public function show(StorePage $storePage) {
        $storePage->load('sections'); // Eager load sections
        return view('admin.store_pages.show', compact('storePage'));
    }

    public function edit(StorePage $storePage) {
        return view('admin.store_pages.edit', compact('storePage'));
    }

    public function update(Request $request, StorePage $storePage) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // Key is usually not updatable once set, but allow if needed:
            // 'key' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9_-]+$/', Rule::unique('store_pages')->ignore($storePage->id)],
        ]);

        $storePage->update($validated);
        return redirect()->route('admin.store-pages.show', $storePage->id)->with('success', 'Store Page updated.');
    }

    public function destroy(StorePage $storePage) {
        // Deleting a page will also delete its sections due to cascade (if set) or manually handle
        // $storePage->sections()->delete(); // If no cascade
        $storePage->delete();
        return redirect()->route('admin.store-pages.index')->with('success', 'Store Page and its sections deleted.');
    }
}