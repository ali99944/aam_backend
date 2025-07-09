<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    // Re-use image handling helpers
    private function handleImageUpload(Request $request, string $fieldName, string $directory, ?string $oldPath = null): ?string {
        if ($request->hasFile($fieldName)) {
           if ($oldPath) Storage::disk('public')->delete($oldPath);
           return $request->file($fieldName)->store($directory, 'public');
        }
        return $oldPath;
    }
    private function deleteImage(?string $path): void { if ($path) Storage::disk('public')->delete($path); }

    public function index(Request $request) {
        $banners = Banner::orderBy('sort_order')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.banners.index', compact('banners'));
    }

    public function create() {
        return view('admin.banners.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Required on create
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|string|max:500|url',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        $validated['is_active'] = $request->has('is_active');
        $validated['image'] = $this->handleImageUpload($request, 'image', 'banners');

        Banner::create($validated);
        return redirect()->route('admin.banners.index')->with('success', 'Banner created successfully.');
    }

    public function edit(Banner $banner) {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Nullable on update
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|string|max:500|url',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        $validated['is_active'] = $request->has('is_active');
        $validated['image'] = $this->handleImageUpload($request, 'image', 'banners', $banner->image);

        if (!$request->hasFile('image')) unset($validated['image']); // Don't update image if not uploaded

        $banner->update($validated);
        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully.');
    }

    public function destroy(Banner $banner) {
        $this->deleteImage($banner->image);
        $banner->delete();
        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted successfully.');
    }
}