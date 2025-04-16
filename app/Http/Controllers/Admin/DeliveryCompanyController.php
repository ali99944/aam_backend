<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator; // Use Validator facade if not using form requests
use Illuminate\Validation\Rule;

class DeliveryCompanyController extends Controller
{
     // Re-use or copy helper functions for image handling
    private function handleImageUpload(Request $request, string $fieldName, string $directory, ?string $oldPath = null): ?string {
        if ($request->hasFile($fieldName)) {
           if ($oldPath) Storage::disk('public')->delete($oldPath);
           return $request->file($fieldName)->store($directory, 'public');
        }
        return $oldPath; // Keep old if no new file
    }
    private function deleteImage(?string $path): void { if ($path) Storage::disk('public')->delete($path); }


    public function index(Request $request) {
        $query = DeliveryCompany::query();
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('contact_email', 'like', $searchTerm)
                  ->orWhere('contact_phone', 'like', $searchTerm);
            });
        }
         if ($request->filled('is_active') && $request->is_active != 'all') {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }
        $companies = $query->orderBy('name')->paginate(15);
        return view('admin.delivery_companies.index', compact('companies'));
    }

    public function create() {
        return view('admin.delivery_companies.create');
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:delivery_companies,name',
            'description' => 'nullable|string|max:2000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:1024', // Max 1MB
            'contact_phone' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:1000',
            'tracking_url_pattern' => 'nullable|string|max:500|url', // Validate as URL
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.delivery-companies.create')->withErrors($validator)->withInput();
        }
        $validated = $validator->validated();
        $validated['is_active'] = $request->has('is_active');
        $validated['logo'] = $this->handleImageUpload($request, 'logo', 'delivery_companies/logos');

        DeliveryCompany::create($validated);
        return redirect()->route('admin.delivery-companies.index')->with('success', 'Delivery Company created.');
    }

    public function edit(DeliveryCompany $deliveryCompany) {
        return view('admin.delivery_companies.edit', compact('deliveryCompany'));
    }

    public function update(Request $request, DeliveryCompany $deliveryCompany) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:delivery_companies,name,' . $deliveryCompany->id,
            'description' => 'nullable|string|max:2000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:1024',
            'contact_phone' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:1000',
            'tracking_url_pattern' => 'nullable|string|max:500|url',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.delivery-companies.edit', $deliveryCompany->id)->withErrors($validator)->withInput();
        }
        $validated = $validator->validated();
        $validated['is_active'] = $request->has('is_active');
        $validated['logo'] = $this->handleImageUpload($request, 'logo', 'delivery_companies/logos', $deliveryCompany->logo);

        // Keep old logo if no new one uploaded
        if (!$request->hasFile('logo') && array_key_exists('logo', $validated) && $validated['logo'] === null) {
             unset($validated['logo']);
        } elseif($validated['logo'] === null) { // Retain if helper returned null (old path)
            $validated['logo'] = $deliveryCompany->logo;
        }

        $deliveryCompany->update($validated);
        return redirect()->route('admin.delivery-companies.index')->with('success', 'Delivery Company updated.');
    }

    public function destroy(DeliveryCompany $deliveryCompany) {
        // IMPORTANT: Add checks here if the company is linked to active orders or delivery personnel
        // Example: if ($deliveryCompany->orders()->whereNotIn('status', ['delivered', 'cancelled'])->exists()) { ... return error ... }

        $this->deleteImage($deliveryCompany->logo); // Delete logo file first
        $deliveryCompany->delete(); // Then delete record
        return redirect()->route('admin.delivery-companies.index')->with('success', 'Delivery Company deleted.');
    }
}