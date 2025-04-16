<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class OfferController extends Controller
{
     // Image handling helpers
    private function handleImageUpload(Request $request, string $fieldName, string $directory, ?string $oldPath = null): ?string {
        if ($request->hasFile($fieldName)) {
           if ($oldPath) Storage::disk('public')->delete($oldPath);
           // Use slug or random name for image
           $slug = Str::slug($request->input('title', Str::random(10)));
           $filename = $slug . '-' . time() . '.' . $request->file($fieldName)->getClientOriginalExtension();
           return $request->file($fieldName)->storeAs($directory, $filename, 'public');
        }
        return $oldPath;
    }
    private function deleteImage(?string $path): void { if ($path) Storage::disk('public')->delete($path); }


    public function index(Request $request) {
        $query = Offer::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('is_active') && $request->is_active != 'all') {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }
         if ($request->filled('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }

        $offers = $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc')->paginate(15);
        $types = Offer::types(); // Get types for filter

        return view('admin.offers.index', compact('offers', 'types'));
    }

    private function getFormData(): array
    {
        return [
            'types' => Offer::types(),
            'categories' => Category::where('is_active', true)->orderBy('name')->pluck('name', 'id'),
            'products' => Product::where('is_public', true)->where('status', Product::STATUS_ACTIVE)->orderBy('name')->limit(500)->pluck('name', 'id'), // Limit for performance
            'brands' => Brand::orderBy('name')->pluck('name', 'id'),
        ];
    }

    public function create() {
        $formData = $this->getFormData();
        return view('admin.offers.create', $formData);
    }

    public function store(Request $request) {
        $validator = $this->validateOffer($request);
        if ($validator->fails()) {
            return redirect()->route('admin.offers.create')->withErrors($validator)->withInput();
        }
        $validated = $validator->validated();
        $validated['is_active'] = $request->has('is_active');
        $validated['image'] = $this->handleImageUpload($request, 'image', 'offers');

        // Ensure linked_id/target_url are null if not relevant
        $this->cleanupLinkFields($validated);

        Offer::create($validated);
        return redirect()->route('admin.offers.index')->with('success', 'Offer created.');
    }

    public function edit(Offer $offer) {
        $formData = $this->getFormData();
        return view('admin.offers.edit', compact('offer'), $formData);
    }

    public function update(Request $request, Offer $offer) {
        $validator = $this->validateOffer($request, $offer->id);
        if ($validator->fails()) {
            return redirect()->route('admin.offers.edit', $offer->id)->withErrors($validator)->withInput();
        }
        $validated = $validator->validated();
        $validated['is_active'] = $request->has('is_active');
        $validated['image'] = $this->handleImageUpload($request, 'image', 'offers', $offer->image);

        // Keep old image if no new one uploaded
        if (!$request->hasFile('image') && array_key_exists('image', $validated) && $validated['image'] === null) {
             unset($validated['image']);
        } elseif($validated['image'] === null) {
            $validated['image'] = $offer->image;
        }

        // Ensure linked_id/target_url are null if not relevant
         $this->cleanupLinkFields($validated);

        $offer->update($validated);
        return redirect()->route('admin.offers.index')->with('success', 'Offer updated.');
    }

    public function destroy(Offer $offer) {
        $this->deleteImage($offer->image);
        $offer->delete();
        return redirect()->route('admin.offers.index')->with('success', 'Offer deleted.');
    }

     // --- Helper Methods ---

    private function validateOffer(Request $request, ?int $offerId = null): \Illuminate\Validation\Validator
    {
        $types = array_keys(Offer::types());

         $rules = [
            'title' => 'required|string|max:255',
            'slug' => [
                'nullable', 'string', 'max:255', 'alpha_dash', // Allow letters, numbers, dashes, underscores
                Rule::unique('offers', 'slug')->ignore($offerId)
            ],
            'description' => 'nullable|string|max:1000',
            'image' => [
                 Rule::requiredIf(!$offerId), // Required on create
                'nullable', // Allow update without changing image
                'image', 'mimes:jpeg,png,jpg,gif,webp,svg', 'max:2048' // Max 2MB
            ],
            'type' => ['required', Rule::in($types)],
            'linked_id' => [
                'nullable',
                'required_if:type,' . Offer::TYPE_CATEGORY . ',' . Offer::TYPE_PRODUCT . ',' . Offer::TYPE_BRAND,
                'integer',
                // Dynamic existence check based on type
                 function ($attribute, $value, $fail) use ($request) {
                     if ($request->input('type') == Offer::TYPE_CATEGORY && !Category::where('id', $value)->exists()) {
                         $fail('Selected category does not exist.');
                     } elseif ($request->input('type') == Offer::TYPE_PRODUCT && !Product::where('id', $value)->exists()) {
                          $fail('Selected product does not exist.');
                     } elseif ($request->input('type') == Offer::TYPE_BRAND && !Brand::where('id', $value)->exists()) {
                          $fail('Selected brand does not exist.');
                     }
                 },
            ],
            'target_url' => [
                 'nullable',
                 'required_if:type,' . Offer::TYPE_GENERIC, // Require if type is generic
                 'url', // Validate as URL
                 'max:500'
            ],
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ];

        return Validator::make($request->all(), $rules,[
             'linked_id.required_if' => 'Please select an item when the type is Category, Product, or Brand.',
             'target_url.required_if' => 'Please enter a Target URL when the type is Generic.',
        ]);
    }

     private function cleanupLinkFields(array &$data): void
    {
        $type = $data['type'] ?? Offer::TYPE_GENERIC;

        if ($type === Offer::TYPE_GENERIC) {
            $data['linked_id'] = null;
        } else {
            $data['target_url'] = null;
        }
        // If linked_id wasn't provided for a type that requires it, validation should have failed
        // but we can double check and nullify here if needed, though maybe not strictly necessary
        // if ($type !== Offer::TYPE_GENERIC && empty($data['linked_id'])) {
        //     $data['linked_id'] = null; // Should already be null or invalid
        // }
    }
}