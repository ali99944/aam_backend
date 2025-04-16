<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    // Image handling helpers
    private function handleImageUpload(Request $request, string $fieldName, string $directory, ?string $oldPath = null): ?string { /* ... copy logic ... */ }
    private function deleteImage(?string $path): void { /* ... copy logic ... */ }

    public function index(Request $request) {
        $query = Supplier::query();

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
             $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('contact_person', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm)
                  ->orWhere('phone', 'like', $searchTerm);
             });
        }
         if ($request->filled('is_active') && $request->is_active != 'all') {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        $suppliers = $query->orderBy('name')->paginate(15);
        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create() {
        return view('admin.suppliers.create');
    }

    public function store(Request $request) {
        $validator = $this->validateSupplier($request);
        if ($validator->fails()) {
            return redirect()->route('admin.suppliers.create')->withErrors($validator)->withInput();
        }
        $validated = $validator->validated();
        $validated['is_active'] = $request->has('is_active');
        $validated['image'] = $this->handleImageUpload($request, 'image', 'suppliers/logos');
        // Balance is not set here, defaults to 0

        Supplier::create($validated);
        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier) {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier) {
         $validator = $this->validateSupplier($request, $supplier->id);
        if ($validator->fails()) {
            return redirect()->route('admin.suppliers.edit', $supplier->id)->withErrors($validator)->withInput();
        }
        $validated = $validator->validated();
        $validated['is_active'] = $request->has('is_active');
        $validated['image'] = $this->handleImageUpload($request, 'image', 'suppliers/logos', $supplier->image);

        // Keep old image if no new one uploaded
        if (!$request->hasFile('image') && array_key_exists('image', $validated) && $validated['image'] === null) unset($validated['image']);
        elseif($validated['image'] === null) $validated['image'] = $supplier->image;

        // Exclude balance from direct update
        unset($validated['balance']);

        $supplier->update($validated);
        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier) {
        // IMPORTANT: Check for related purchase orders, payments, products before deleting.
        // Example: if ($supplier->purchaseOrders()->where('status', '!=', 'completed')->exists()) { return back()->with('error', '...'); }
        // Example: if ($supplier->balance != 0) { return back()->with('error', 'Cannot delete supplier with non-zero balance.'); }

        $this->deleteImage($supplier->image);
        $supplier->delete();
        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier deleted successfully.');
    }

     // --- Validation Helper ---
    private function validateSupplier(Request $request, ?int $supplierId = null): \Illuminate\Validation\Validator
    {
        $rules = [
            'name' => ['required', 'string', 'max:255', Rule::unique('suppliers')->ignore($supplierId)],
            'description' => 'nullable|string|max:2000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:1024',
            'contact_person' => 'nullable|string|max:255',
            'email' => ['nullable','string', 'email', 'max:255', Rule::unique('suppliers')->ignore($supplierId)],
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
            'website' => 'nullable|url|max:255',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string|max:2000',
        ];
        return Validator::make($request->all(), $rules);
    }

    // --- Future methods for managing balance ---
    // public function adjustBalance(Request $request, Supplier $supplier) { ... }
    // public function recordPayment(Request $request, Supplier $supplier) { ... }

}