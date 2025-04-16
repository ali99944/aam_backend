<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage; // Import Storage facade
use Illuminate\Support\Str; // Import Str facade

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::query();
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }
        $categories = $query->orderBy('name')->paginate(15);
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Max 2MB standard image
            'icon_image' => 'nullable|file|mimes:svg|max:512', // Max 512KB SVG file
        ], [
            'icon_image.mimes' => 'The icon must be a file of type: svg.', // Custom message for SVG
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.categories.create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $validatedData = $validator->validated();
        $validatedData['is_active'] = $request->has('is_active');

        // Handle Cover Image Upload
        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('categories/covers', 'public');
            $validatedData['cover_image'] = $path;
        }

        // Handle Icon Image (SVG) Upload
        if ($request->hasFile('icon_image')) {
            $path = $request->file('icon_image')->store('categories/icons', 'public');
            $validatedData['icon_image'] = $path;
        }

        Category::create($validatedData);

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
         return redirect()->route('admin.categories.edit', $category);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
         $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Nullable on update
            'icon_image' => 'nullable|file|mimes:svg|max:512', // Nullable on update
        ], [
            'icon_image.mimes' => 'The icon must be a file of type: svg.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.categories.edit', $category->id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $validatedData = $validator->validated();
        $validatedData['is_active'] = $request->has('is_active');

        // Handle Cover Image Update
        if ($request->hasFile('cover_image')) {
            // Delete old image if it exists
            if ($category->cover_image) {
                Storage::disk('public')->delete($category->cover_image);
            }
            // Store new image
            $path = $request->file('cover_image')->store('categories/covers', 'public');
            $validatedData['cover_image'] = $path;
        }

        // Handle Icon Image (SVG) Update
        if ($request->hasFile('icon_image')) {
            // Delete old icon if it exists
            if ($category->icon_image) {
                Storage::disk('public')->delete($category->icon_image);
            }
            // Store new icon
            $path = $request->file('icon_image')->store('categories/icons', 'public');
            $validatedData['icon_image'] = $path;
        }

        $category->update($validatedData);

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Optional: Add checks for related items

        // Delete associated images before deleting the category record
        if ($category->cover_image) {
            Storage::disk('public')->delete($category->cover_image);
        }
        if ($category->icon_image) {
            Storage::disk('public')->delete($category->icon_image);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Category deleted successfully.');
    }
}