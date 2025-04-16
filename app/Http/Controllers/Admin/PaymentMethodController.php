<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str; // For slug generation

class PaymentMethodController extends Controller
{
    private function handleImageUpload(Request $request, string $fieldName, string $directory, ?string $oldPath = null): ?string
    {
        if ($request->hasFile($fieldName)) {
            // Delete old image if exists and new one is uploaded
            if ($oldPath) {
                Storage::disk('public')->delete($oldPath);
            }
            // Store new image
            return $request->file($fieldName)->store($directory, 'public');
        }
        // Return old path if no new file uploaded (relevant for updates)
        return $oldPath;
    }

    // Helper function for file deletion
    private function deleteImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
    public function index(Request $request) {
        $query = PaymentMethod::query();
        // Add filtering (search, status) as needed (similar to previous index methods)
        $methods = $query->orderBy('display_order')->orderBy('name')->paginate(15);
        return view('admin.payment_methods.index', compact('methods'));
    }

    public function create() {
        return view('admin.payment_methods.create');
    }

    public function store(Request $request) {
        $validator = $this->validatePaymentMethod($request);
        if ($validator->fails()) {
            return redirect()->route('admin.payment-methods.create')->withErrors($validator)->withInput();
        }
        $validated = $validator->validated();

        // Handle booleans
        $validated['is_enabled'] = $request->has('is_enabled');
        $validated['is_default'] = $request->has('is_default');
        $validated['is_test_mode'] = $request->has('is_test_mode');
        $validated['is_online'] = $request->has('is_online');

        // Generate slug if empty
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
            // Check uniqueness again for auto-generated slug
            if (PaymentMethod::where('slug', $validated['slug'])->exists()) {
                 return redirect()->route('admin.payment-methods.create')
                                ->withErrors(['slug' => 'Generated slug already exists. Please provide a unique slug.'])
                                ->withInput();
            }
        }

        // Handle image upload
        $validated['image'] = $this->handleImageUpload($request, 'image', 'payment_methods/logos');

        // Create (mutators will handle encryption & observer handles default)
        PaymentMethod::create($validated);
        return redirect()->route('admin.payment-methods.index')->with('success', 'Payment Method created.');
    }

    public function edit(PaymentMethod $paymentMethod) {
        // Decryption happens via accessors automatically when accessing $paymentMethod->api_key etc. in the view
        return view('admin.payment_methods.edit', compact('paymentMethod'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod) {
        $validator = $this->validatePaymentMethod($request, $paymentMethod->id);
         if ($validator->fails()) {
            return redirect()->route('admin.payment-methods.edit', $paymentMethod->id)->withErrors($validator)->withInput();
        }
        $validated = $validator->validated();

        // Handle booleans
        $validated['is_enabled'] = $request->has('is_enabled');
        $validated['is_default'] = $request->has('is_default');
        $validated['is_test_mode'] = $request->has('is_test_mode');
        $validated['is_online'] = $request->has('is_online');

        // Handle image upload/update
        $validated['image'] = $this->handleImageUpload($request, 'image', 'payment_methods/logos', $paymentMethod->image);
        if (!$request->hasFile('image') && array_key_exists('image', $validated) && $validated['image'] === null) unset($validated['image']);
        elseif($validated['image'] === null) $validated['image'] = $paymentMethod->image;

        // Update (mutators will handle encryption & observer handles default)
        $paymentMethod->update($validated);
        return redirect()->route('admin.payment-methods.index')->with('success', 'Payment Method updated.');
    }

    public function destroy(PaymentMethod $paymentMethod) {
        // !! IMPORTANT !!: Add checks before deleting!
        // - Cannot delete the default method if it's the only enabled one?
        // - Check if used in past orders (maybe just disable instead of delete?)
        if($paymentMethod->is_default) {
             return back()->with('error', 'Cannot delete the default payment method.');
        }
        // Add more checks as needed...

        $this->deleteImage($paymentMethod->image);
        $paymentMethod->delete();
        return redirect()->route('admin.payment-methods.index')->with('success', 'Payment Method deleted.');
    }

    // --- Validation Helper ---
    private function validatePaymentMethod(Request $request, ?int $methodId = null): \Illuminate\Validation\Validator
    {
        $rules = [
            'name' => ['required', 'string', 'max:255', Rule::unique('payment_methods')->ignore($methodId)],
            'code' => ['required', 'string', 'max:50', 'regex:/^[a-z0-9_]+$/', Rule::unique('payment_methods')->ignore($methodId)],
            'description' => 'nullable|string|max:1000',
            'image' => ['nullable', Rule::requiredIf(!$methodId), 'image', 'mimes:jpeg,png,jpg,gif,webp,svg', 'max:512'], // Required on create, max 512KB
            'slug' => ['nullable','string', 'max:100', 'regex:/^[a-z0-9_-]+$/', Rule::unique('payment_methods')->ignore($methodId)],
            'gateway_provider' => 'nullable|string|max:100',
            'supported_currencies' => 'nullable|string|max:255', // Simple text for now
            'is_default' => 'nullable|boolean',
            'is_enabled' => 'nullable|boolean',
            'is_test_mode' => 'nullable|boolean',
            'is_online' => 'nullable|boolean',
            'credentials' => 'nullable|string', // Store as JSON string from textarea? Or validate specific keys if needed
            'display_order' => 'required|integer|min:0',
            'instructions' => 'nullable|string|max:2000',
            'api_key' => 'nullable|string|max:500',
            'api_secret' => 'nullable|string|max:500',
            'client_id' => 'nullable|string|max:500',
            'client_secret' => 'nullable|string|max:500',
            'merchant_id' => 'nullable|string|max:500',
            'merchant_key' => 'nullable|string|max:500',
            'redirect_url' => 'nullable|url|max:500',
        ];
         $messages = [
            'code.regex' => 'Code must contain only lowercase letters, numbers, and underscores.',
            'slug.regex' => 'Slug must contain only lowercase letters, numbers, underscores, and hyphens.',
            'image.required' => 'Image is required when creating a new payment method.',
        ];

        // Special validation for credentials if expecting JSON
        // Add this logic if you decide credentials *must* be JSON
        // $rules['credentials'] = ['nullable', 'json'];

        return Validator::make($request->all(), $rules, $messages);
    }
}