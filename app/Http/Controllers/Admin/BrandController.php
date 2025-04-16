<?php // app/Http/Controllers/Admin/BrandController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
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
    private function deleteImage(?string $path): void { /* ... copy logic ... */ }

    public function index(Request $request)
    {
        $query = Brand::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $brands = $query->orderBy('name')->paginate(15);
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:brands,name',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:1024', // Max 1MB
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.brands.create')->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();
        $validatedData['image'] = $this->handleImageUpload($request, 'image', 'brands/images');

        Brand::create($validatedData);
        return redirect()->route('admin.brands.index')->with('success', 'Brand created successfully.');
    }

    public function show(Brand $brand) { return redirect()->route('admin.brands.edit', $brand); }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:1024',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.brands.edit', $brand->id)->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();
        $validatedData['image'] = $this->handleImageUpload($request, 'image', 'brands/images', $brand->image);

        if (!$request->hasFile('image') && array_key_exists('image', $validatedData) && $validatedData['image'] === null) unset($validatedData['image']);
        else if ($validatedData['image'] === null) $validatedData['image'] = $brand->image;


        $brand->update($validatedData);
        return redirect()->route('admin.brands.index')->with('success', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand)
    {
        $this->deleteImage($brand->image);
        $brand->delete();
        return redirect()->route('admin.brands.index')->with('success', 'Brand deleted successfully.');
    }
}