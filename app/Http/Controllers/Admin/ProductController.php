<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\Brand;
use App\Models\Discount;
use App\Models\ProductImage; // Import related models
use App\Models\ProductSpec;
use App\Models\ProductAddon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // For database transactions
use Illuminate\Support\Facades\Log; // For logging errors
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D; // Import DNS1D facade
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    // --- Index Page ---
    public function index(Request $request)
    {
        $query = Product::with(['subCategory', 'brand']); // Eager load basic relationships

        // --- Filtering Logic ---
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('sku_code', 'like', $searchTerm)
                  ->orWhereHas('brand', fn($bq) => $bq->where('name', 'like', $searchTerm))
                  ->orWhereHas('subCategory', fn($sq) => $sq->where('name', 'like', $searchTerm));
            });
        }
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }
        if ($request->filled('sub_category_id')) {
             $query->where('sub_category_id', $request->sub_category_id);
        }
         if ($request->filled('status') && $request->status != 'all') {
             $query->where('status', $request->status);
        }
        if ($request->filled('is_public') && $request->is_public != 'all') {
             $query->where('is_public', filter_var($request->is_public, FILTER_VALIDATE_BOOLEAN));
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(20);

        // Data for filters
        $brands = Brand::orderBy('name')->pluck('name', 'id');
        $subCategories = SubCategory::orderBy('name')->pluck('name', 'id'); // Consider grouping by category?
        $statuses = Product::statuses();

        return view('admin.products.index', compact('products', 'brands', 'subCategories', 'statuses'));
    }

    // --- Create Form ---
    public function create()
    {
        $subCategories = SubCategory::whereHas('category', fn($q) => $q->where('is_active', true))
                                    ->where('is_active', true)->orderBy('name')->get()->groupBy('category.name'); // Grouped
        $brands = Brand::orderBy('name')->pluck('name', 'id');
        $discounts = Discount::valid()->orderBy('name')->pluck('name', 'id'); // Only show valid discounts
        $statuses = Product::statuses();

        return view('admin.products.create', compact('subCategories', 'brands', 'discounts', 'statuses'));
    }

    // --- Store New Product ---
    public function store(Request $request)
    {
        $validator = $this->validateProduct($request);
        if ($validator->fails()) {
            return redirect()->route('admin.products.create')->withErrors($validator)->withInput();
        }
        $validated = $validator->validated();

        DB::beginTransaction(); // Start Transaction
        try {
            // 1. Handle Main Image Upload
            $mainImagePath = null;
            if ($request->hasFile('main_image')) {
                $mainImagePath = $request->file('main_image')->store('products/main', 'public');
            }

            // 2. Create Product Record
            $product = Product::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'main_image' => $mainImagePath,
                'cost_price' => $validated['cost_price'],
                'sell_price' => $validated['sell_price'],
                'stock' => $validated['stock'] ?? 0,
                'lower_stock_warn' => $validated['lower_stock_warn'] ?? 0,
                'sku_code' => $validated['sku_code'] ?? Str::random(10), // Generate SKU if empty
                'sub_category_id' => $validated['sub_category_id'],
                'brand_id' => $validated['brand_id'],
                'discount_id' => $validated['discount_id'] ?? null,
                'status' => $validated['status'],
                'is_public' => $request->has('is_public'),
                'overall_rating' => 0,
                'barcode' => DNS1D::getBarcodePNG($validated['sku_code'] ?? $product->sku_code, 'C128') // Generate and save barcode
            ]);

            // 3. Handle Additional Images
            if ($request->hasFile('additional_images')) {
                foreach ($request->file('additional_images') as $file) {
                    if ($file->isValid()) {
                        $path = $file->store('products/additional', 'public');
                        $product->images()->create(['src' => $path]);
                    }
                }
            }

            // 4. Handle Specs
            if (isset($validated['specs']) && is_array($validated['specs'])) {
                foreach ($validated['specs'] as $specData) {
                    if (!empty($specData['name']) && !empty($specData['value'])) {
                         $product->specs()->create([
                             'name' => $specData['name'],
                             'value' => $specData['value'],
                             'is_active' => true, // Default active on creation
                         ]);
                    }
                }
            }

            // 5. Handle Addons
            if (isset($validated['addons']) && is_array($validated['addons'])) {
                 foreach ($validated['addons'] as $addonData) {
                     if (!empty($addonData['name']) && isset($addonData['price'])) {
                         $product->addons()->create([
                             'name' => $addonData['name'],
                             'price' => $addonData['price'],
                              'is_active' => true, // Default active on creation
                         ]);
                     }
                 }
            }

            DB::commit(); // Commit Transaction
            return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback Transaction on error
            Log::error("Error creating product: " . $e->getMessage());
            // Delete uploaded main image if creation failed
            if (isset($mainImagePath) && Storage::disk('public')->exists($mainImagePath)) {
                 Storage::disk('public')->delete($mainImagePath);
            }
            // Note: Deleting additional images on failure is more complex here

            return redirect()->route('admin.products.create')
                         ->with('error', 'Failed to create product. Please try again. ' . $e->getMessage())
                         ->withInput();
        }
    }

    // --- Edit Form ---
     public function edit(Product $product)
    {
        $product->load(['images', 'specs', 'addons']); // Load relationships
        $subCategories = SubCategory::whereHas('category', fn($q) => $q->where('is_active', true))
                                    ->where('is_active', true)->orderBy('name')->get()->groupBy('category.name');
        $brands = Brand::orderBy('name')->pluck('name', 'id');
        // Include currently assigned discount even if invalid now, plus all valid ones
        $discounts = Discount::valid()
                            ->orWhere('id', $product->discount_id)
                            ->orderBy('name')
                            ->pluck('name', 'id');
        $statuses = Product::statuses();

        return view('admin.products.edit', compact('product', 'subCategories', 'brands', 'discounts', 'statuses'));
    }

    // --- Update Existing Product ---
    public function update(Request $request, Product $product)
    {
        $validator = $this->validateProduct($request, $product->id);
        if ($validator->fails()) {
            return redirect()->route('admin.products.edit', $product->id)->withErrors($validator)->withInput();
        }
        $validated = $validator->validated();

        DB::beginTransaction();
        try {
            // 1. Handle Main Image Update
            $mainImagePath = $product->main_image;
            if ($request->hasFile('main_image')) {
                 if ($mainImagePath) Storage::disk('public')->delete($mainImagePath); // Delete old
                $mainImagePath = $request->file('main_image')->store('products/main', 'public');
            }
             // Add logic here if you have a "remove_main_image" checkbox
            // elseif ($request->has('remove_main_image')) {
            //     if ($mainImagePath) Storage::disk('public')->delete($mainImagePath);
            //     $mainImagePath = null;
            // }


            // 2. Update Product Core Details
            $updateData = [
                'name' => $validated['name'],
                'description' => $validated['description'],
                'main_image' => $mainImagePath,
                'cost_price' => $validated['cost_price'],
                'sell_price' => $validated['sell_price'],
                'stock' => $validated['stock'] ?? $product->stock, // Keep old if not provided?
                'lower_stock_warn' => $validated['lower_stock_warn'] ?? $product->lower_stock_warn,
                'sub_category_id' => $validated['sub_category_id'],
                'brand_id' => $validated['brand_id'],
                'discount_id' => $validated['discount_id'] ?? null, // Allow unsetting discount
                'status' => $validated['status'],
                'is_public' => $request->has('is_public'),
            ];

            // Handle SKU and Barcode update
            $newSkuCode = $validated['sku_code'] ?? $product->sku_code;
            if ($newSkuCode !== $product->sku_code || ($newSkuCode && !$product->barcode)) {
                $updateData['sku_code'] = $newSkuCode;
                $updateData['barcode'] = DNS1D::getBarcodePNG($newSkuCode, 'C128');
            } elseif (isset($validated['sku_code']) && $validated['sku_code'] === $product->sku_code && !$product->barcode && $product->sku_code) {
                // Case: SKU hasn't changed, but barcode was missing and SKU exists, so generate it.
                $updateData['barcode'] = DNS1D::getBarcodePNG($product->sku_code, 'C128');
            }

            $product->update($updateData);

            // 3. Handle Additional Images (More complex: Add new, Remove existing)
            // Delete marked images
            if ($request->filled('remove_images') && is_array($request->input('remove_images'))) {
                $imagesToDelete = ProductImage::where('product_id', $product->id)
                                            ->whereIn('id', $request->input('remove_images'))
                                            ->get();
                foreach ($imagesToDelete as $img) {
                    Storage::disk('public')->delete($img->src);
                    $img->delete();
                }
            }
             // Add new images
            if ($request->hasFile('additional_images')) {
                 foreach ($request->file('additional_images') as $file) {
                    if ($file->isValid()) {
                        $path = $file->store('products/additional', 'public');
                        $product->images()->create(['src' => $path]);
                    }
                 }
            }

            // 4. Handle Specs (Syncing: Update existing, add new, delete removed)
            $this->syncRelatedData($product, 'specs', $validated['specs'] ?? [], ['name', 'value']);

            // 5. Handle Addons (Syncing)
            $this->syncRelatedData($product, 'addons', $validated['addons'] ?? [], ['name', 'price']);


            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating product ID {$product->id}: " . $e->getMessage());
            return redirect()->route('admin.products.edit', $product->id)
                         ->with('error', 'Failed to update product. Please try again. ' . $e->getMessage())
                         ->withInput();
        }
    }

    // --- Delete Product ---
    public function destroy(Product $product)
    {
        DB::beginTransaction();
        try {
            // Delete Main Image
            if ($product->main_image) Storage::disk('public')->delete($product->main_image);

            // Delete Additional Images
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->src);
                $image->delete(); // Delete record
            }

            // Specs and Addons will be deleted via cascade if set up in migration,
            // otherwise, delete them manually:
            // $product->specs()->delete();
            // $product->addons()->delete();

            // Finally, delete the product
            $product->delete();

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error deleting product ID {$product->id}: " . $e->getMessage());
            return redirect()->route('admin.products.index')->with('error', 'Failed to delete product.');
        }
    }


    // --- Helper: Validation Rules ---
    private function validateProduct(Request $request, ?int $productId = null): \Illuminate\Validation\Validator
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:5000', // Increased max length
            'sub_category_id' => 'required|exists:sub_categories,id',
            'brand_id' => 'required|exists:brands,id',
            'discount_id' => 'nullable|exists:discounts,id',
            'cost_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0', // Add comparison rule? |gte:cost_price
            'stock' => 'required|integer|min:0',
            'lower_stock_warn' => 'nullable|integer|min:0',
            'sku_code' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('products', 'sku_code')->ignore($productId)
            ],
            'status' => ['required', Rule::in(array_keys(Product::statuses()))],
            'is_public' => 'nullable|boolean', // Handled by $request->has()
            'main_image' => [
                Rule::requiredIf(!$productId), // Required only on create
                'nullable', // Allow nullable on update
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:2048' // Max 2MB
            ],
             'additional_images.*' => [ // Validate each file in the array
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:2048'
            ],
            // Validation for dynamic fields (specs, addons)
            'specs' => 'nullable|array',
            'specs.*.name' => 'required_with:specs.*.value|nullable|string|max:255',
            'specs.*.value' => 'required_with:specs.*.name|nullable|string|max:255',
            // Optional: Add 'specs.*.id' => 'nullable|exists:product_specs,id' if updating specific IDs

             'addons' => 'nullable|array',
            'addons.*.name' => 'required_with:addons.*.price|nullable|string|max:255',
            'addons.*.price' => 'required_with:addons.*.name|nullable|numeric|min:0',
             // Optional: Add 'addons.*.id' => 'nullable|exists:product_addons,id'
        ];

        return Validator::make($request->all(), $rules, [
            'additional_images.*.image' => 'Each additional image must be a valid image file.',
            'additional_images.*.mimes' => 'Invalid file type for additional image.',
            'additional_images.*.max' => 'Additional image size exceeds limit.',
            'specs.*.name.required_with' => 'Spec name is required when value is present.',
            'specs.*.value.required_with' => 'Spec value is required when name is present.',
            'addons.*.name.required_with' => 'Addon name is required when price is present.',
            'addons.*.price.required_with' => 'Addon price is required when name is present.',
        ]);
    }

    // --- Helper: Sync Related Data (Specs/Addons) ---
    private function syncRelatedData(Product $product, string $relation, array $data, array $identifyingKeys): void
    {
        $existingIds = $product->$relation()->pluck('id')->all();
        $incomingIds = [];
        $newData = [];

        foreach ($data as $itemData) {
            // Check if essential keys are present and not empty
            $isValid = true;
            foreach ($identifyingKeys as $key) {
                 if (!isset($itemData[$key]) || $itemData[$key] === '' || $itemData[$key] === null) {
                      $isValid = false;
                      break;
                 }
            }
            if (!$isValid) continue; // Skip invalid entries

            if (isset($itemData['id']) && !empty($itemData['id'])) {
                // Update existing item
                $id = $itemData['id'];
                $item = $product->$relation()->find($id);
                if ($item) {
                    $updateData = collect($itemData)->only($item->getFillable())->toArray();
                    $item->update($updateData);
                    $incomingIds[] = (int)$id;
                }
            } else {
                // Prepare new item data (only fillable attributes)
                $relatedModel = $product->$relation()->getRelated();
                $fillableData = collect($itemData)->only($relatedModel->getFillable())->toArray();
                // Ensure default active state if applicable
                if (in_array('is_active', $relatedModel->getFillable()) && !isset($fillableData['is_active'])) {
                    $fillableData['is_active'] = true;
                }

                $newData[] = $fillableData;
            }
        }

        // Create new items
        if (!empty($newData)) {
            $product->$relation()->createMany($newData);
        }

        // Delete items that were not in the incoming data
        $idsToDelete = array_diff($existingIds, $incomingIds);
        if (!empty($idsToDelete)) {
            $product->$relation()->whereIn('id', $idsToDelete)->delete();
        }
    }

    public function toggleFeatured(Product $product)
    {
        try {
            $product->is_featured = !$product->is_featured;
            $product->save();

            $message = $product->is_featured ? 'Product marked as featured.' : 'Product removed from featured.';
            return back()->with('success', $message); // Redirect back to previous page (index or edit)

        } catch (\Exception $e) {
            Log::error("Error toggling featured status for product {$product->id}: " . $e->getMessage());
            return back()->with('error', 'Failed to update featured status.');
        }
    }

} // End of Controller