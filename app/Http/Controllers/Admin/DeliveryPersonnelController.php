<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryPersonnel;
use App\Models\DeliveryCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password; // For password rules

class DeliveryPersonnelController extends Controller
{
    // Re-use helper functions for image handling
    private function handleImageUpload(Request $request, string $fieldName, string $directory, ?string $oldPath = null): ?string {
        if ($request->hasFile($fieldName)) {
           if ($oldPath) Storage::disk('public')->delete($oldPath);
           return $request->file($fieldName)->store($directory, 'public');
        }
        return $oldPath;
    }
    private function deleteImage(?string $path): void { if ($path) Storage::disk('public')->delete($path); }


    public function index(Request $request) {
        $query = DeliveryPersonnel::with('deliveryCompany');

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
             $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm)
                  ->orWhere('phone', 'like', $searchTerm);
             });
        }
        if ($request->filled('delivery_company_id')) {
             // Handle 'independent' filter case
            if ($request->delivery_company_id === 'independent') {
                 $query->whereNull('delivery_company_id');
            } else {
                 $query->where('delivery_company_id', $request->delivery_company_id);
            }
        }
         if ($request->filled('is_active') && $request->is_active != 'all') {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        $deliveryPersonnel = $query->orderBy('name')->paginate(15);
        $companies = DeliveryCompany::where('is_active', true)->orderBy('name')->pluck('name', 'id');

        return view('admin.delivery_personnel.index', compact('deliveryPersonnel', 'companies'));
    }

    public function create() {
        $companies = DeliveryCompany::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        return view('admin.delivery_personnel.create', compact('companies'));
    }

    public function store(Request $request) {
         $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:delivery_personnel,email',
            'phone' => 'required|string|max:50|unique:delivery_personnel,phone',
            'password' => ['required', 'confirmed', Password::min(8)], // Require password on creation
            'delivery_company_id' => 'nullable|exists:delivery_companies,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024', // Max 1MB
            'vehicle_type' => 'nullable|string|max:100',
            'vehicle_plate_number' => 'nullable|string|max:50',
            'national_id_or_iqama' => 'nullable|string|max:50|unique:delivery_personnel,national_id_or_iqama',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.delivery-personnel.create')->withErrors($validator)->withInput();
        }
        $validated = $validator->validated();
        $validated['is_active'] = $request->has('is_active');
        $validated['avatar'] = $this->handleImageUpload($request, 'avatar', 'delivery_personnel/avatars');
        $validated['password'] = Hash::make($validated['password']); // Hash the password

        DeliveryPersonnel::create($validated);
        return redirect()->route('admin.delivery-personnel.index')->with('success', 'Delivery Person created.');
    }

    public function edit(DeliveryPersonnel $deliveryPersonnel) { // Use singular model name for binding
        $companies = DeliveryCompany::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        return view('admin.delivery_personnel.edit', compact('deliveryPersonnel', 'companies'));
    }

    public function update(Request $request, DeliveryPersonnel $deliveryPersonnel) {
         $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:delivery_personnel,email,' . $deliveryPersonnel->id,
            'phone' => 'required|string|max:50|unique:delivery_personnel,phone,' . $deliveryPersonnel->id,
            'password' => ['nullable', 'confirmed', Password::min(8)], // Optional on update
            'delivery_company_id' => 'nullable|exists:delivery_companies,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'vehicle_type' => 'nullable|string|max:100',
            'vehicle_plate_number' => 'nullable|string|max:50',
            'national_id_or_iqama' => 'nullable|string|max:50|unique:delivery_personnel,national_id_or_iqama,' . $deliveryPersonnel->id,
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.delivery-personnel.edit', $deliveryPersonnel->id)->withErrors($validator)->withInput();
        }
        $validated = $validator->validated();
        $validated['is_active'] = $request->has('is_active');
        $validated['avatar'] = $this->handleImageUpload($request, 'avatar', 'delivery_personnel/avatars', $deliveryPersonnel->avatar);

        // Handle password update (only if provided)
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']); // Don't update password if field is empty
        }

        // Handle avatar update (keep old if no new one)
        if (!$request->hasFile('avatar') && array_key_exists('avatar', $validated) && $validated['avatar'] === null) {
            unset($validated['avatar']);
        } elseif($validated['avatar'] === null) {
            $validated['avatar'] = $deliveryPersonnel->avatar;
        }

        $deliveryPersonnel->update($validated);
        return redirect()->route('admin.delivery-personnel.index')->with('success', 'Delivery Person updated.');
    }

    public function destroy(DeliveryPersonnel $deliveryPersonnel) {
        // IMPORTANT: Check if this person has active/assigned orders. Prevent deletion if so.
        // Example: if ($deliveryPersonnel->assignedOrders()->where('status', '!=', 'delivered')->exists()) { ... return error ... }

        $this->deleteImage($deliveryPersonnel->avatar); // Delete avatar
        $deliveryPersonnel->delete(); // Delete record
        return redirect()->route('admin.delivery-personnel.index')->with('success', 'Delivery Person deleted.');
    }
}