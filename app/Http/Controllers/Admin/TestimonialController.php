<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller
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

    public function index() {
        $testimonials = Testimonial::orderBy('sort_order')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function create() {
        return view('admin.testimonials.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title_or_company' => 'nullable|string|max:255',
            'quote' => 'required|string|max:2000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'rating' => 'nullable|integer|min:1|max:5',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        $validated['is_active'] = $request->has('is_active');
        $validated['avatar'] = $this->handleImageUpload($request, 'avatar', 'testimonials/avatars');

        Testimonial::create($validated);
        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial created successfully.');
    }

    public function edit(Testimonial $testimonial) {
        return view('admin.testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title_or_company' => 'nullable|string|max:255',
            'quote' => 'required|string|max:2000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'rating' => 'nullable|integer|min:1|max:5',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        $validated['is_active'] = $request->has('is_active');
        $validated['avatar'] = $this->handleImageUpload($request, 'avatar', 'testimonials/avatars', $testimonial->avatar);

        if (!$request->hasFile('avatar')) unset($validated['avatar']);

        $testimonial->update($validated);
        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial updated successfully.');
    }

    public function destroy(Testimonial $testimonial) {
        $this->deleteImage($testimonial->avatar);
        $testimonial->delete();
        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial deleted successfully.');
    }
}