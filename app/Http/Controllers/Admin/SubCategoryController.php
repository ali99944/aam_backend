<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use App\Models\Category; // Need Category model for dropdown
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class SubCategoryController extends Controller
{
    // Helper function for file handling
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


    public function index(Request $request)
    {
        $query = SubCategory::with('category'); // Eager load category relationship

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhereHas('category', function ($catQuery) use ($searchTerm) {
                      $catQuery->where('name', 'like', $searchTerm); // Search by parent category name too
                  });
            });
        }
        if ($request->filled('category_id')) {
             $query->where('category_id', $request->category_id);
        }


        $subCategories = $query->orderBy('name')->paginate(15);
        $categories = Category::orderBy('name')->pluck('name', 'id'); // For filter dropdown

        return view('admin.subcategories.index', compact('subCategories', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        return view('admin.subcategories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255', // Consider unique within category? Rule::unique('sub_categories')->where('category_id', $request->category_id)
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'icon_image' => 'nullable|file|mimes:svg|max:512', // Optional icon
        ], [
            'icon_image.mimes' => 'The icon must be a file of type: svg.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.subcategories.create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $validatedData = $validator->validated();
        $validatedData['is_active'] = $request->has('is_active');

        // Handle Uploads
        $validatedData['cover_image'] = $this->handleImageUpload($request, 'cover_image', 'subcategories/covers');
        $validatedData['icon_image'] = $this->handleImageUpload($request, 'icon_image', 'subcategories/icons');

        SubCategory::create($validatedData);

        return redirect()->route('admin.subcategories.index')
                         ->with('success', 'Sub Category created successfully.');
    }

    // Show method typically redirects to edit for admin panels
    public function show(SubCategory $subCategory)
    {
        return redirect()->route('admin.subcategories.edit', $subCategory);
    }

    public function edit(SubCategory $subCategory)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        return view('admin.subcategories.edit', compact('subCategory', 'categories'));
    }

    public function update(Request $request, SubCategory $subCategory)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255', // Add unique rule if needed
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'icon_image' => 'nullable|file|mimes:svg|max:512',
        ], [
            'icon_image.mimes' => 'The icon must be a file of type: svg.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.subcategories.edit', $subCategory->id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $validatedData = $validator->validated();
        $validatedData['is_active'] = $request->has('is_active');

        // Handle Uploads & Pass Old Paths
        $validatedData['cover_image'] = $this->handleImageUpload($request, 'cover_image', 'subcategories/covers', $subCategory->cover_image);
        $validatedData['icon_image'] = $this->handleImageUpload($request, 'icon_image', 'subcategories/icons', $subCategory->icon_image);

        // If a new file wasn't uploaded, the validated data won't contain the key,
        // so we explicitly keep the old value if no new file and no old path was passed
        if (!$request->hasFile('cover_image') && array_key_exists('cover_image', $validatedData) && $validatedData['cover_image'] === null) {
             unset($validatedData['cover_image']); // Don't update if no new file
        } else if ($validatedData['cover_image'] === null) {
            // If handleImageUpload returned null (meaning keep old path), but the field exists,
            // it means we should retain the existing value explicitly IF $request didn't specifically try to clear it.
            // This logic might need refinement based on how you want clearing images to work (e.g., a separate checkbox)
             $validatedData['cover_image'] = $subCategory->cover_image;
        }
        // Repeat for icon_image
         if (!$request->hasFile('icon_image') && array_key_exists('icon_image', $validatedData) && $validatedData['icon_image'] === null) {
             unset($validatedData['icon_image']);
        } else if ($validatedData['icon_image'] === null) {
             $validatedData['icon_image'] = $subCategory->icon_image;
        }


        $subCategory->update($validatedData);

        return redirect()->route('admin.subcategories.index')
                         ->with('success', 'Sub Category updated successfully.');
    }

    public function destroy(SubCategory $subCategory)
    {
        // Delete associated images
        $this->deleteImage($subCategory->cover_image);
        $this->deleteImage($subCategory->icon_image);

        $subCategory->delete();

        return redirect()->route('admin.subcategories.index')
                         ->with('success', 'Sub Category deleted successfully.');
    }
}