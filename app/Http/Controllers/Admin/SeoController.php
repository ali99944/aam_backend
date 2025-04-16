<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SeoController extends Controller
{
    // Helper for image handling (can be moved to a Trait or Base Controller)
    private function handleImageUpload(Request $request, string $fieldName, string $directory, ?string $oldPath = null): ?string {
        if ($request->hasFile($fieldName)) {
            if ($oldPath) Storage::disk('public')->delete($oldPath);
            return $request->file($fieldName)->store($directory, 'public');
        }
        // If remove checkbox is checked (assuming name="remove_{$fieldName}")
        if ($request->has("remove_{$fieldName}") && $oldPath) {
             Storage::disk('public')->delete($oldPath);
             return null;
        }
        return $oldPath; // Keep old path if no new file and not removed
    }
    private function deleteImage(?string $path): void {
        if ($path) Storage::disk('public')->delete($path);
    }

    /**
     * Display a listing of the 'page' type SEO records.
     */
    public function index(Request $request)
    {
        $query = Seo::where('type', Seo::TYPE_PAGE); // Only show pages

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('key', 'like', $searchTerm)
                  ->orWhere('title', 'like', $searchTerm);
            });
        }

        $seoPages = $query->orderBy('name')->paginate(20);
        return view('admin.seo.index', compact('seoPages'));
    }

    /**
     * Show the form for creating a new 'page' type SEO record.
     */
    public function create()
    {
        return view('admin.seo.create');
    }

    /**
     * Store a newly created 'page' type SEO record in storage.
     */
    public function store(Request $request)
    {
        $validator = $this->validateSeo($request);
        if ($validator->fails()) {
            return redirect()->route('admin.seo.create')->withErrors($validator)->withInput();
        }
        $validated = $validator->validated();

        // Force type to 'page' for this controller
        $validated['type'] = Seo::TYPE_PAGE;

        // Handle image uploads
        $validated['og_image'] = $this->handleImageUpload($request, 'og_image', 'seo/og');
        $validated['twitter_image'] = $this->handleImageUpload($request, 'twitter_image', 'seo/twitter'); // Use same dir or different?

        Seo::create($validated);

        return redirect()->route('admin.seo.index')->with('success', 'SEO Page settings created successfully.');
    }


    /**
     * Show the form for editing the specified SEO record.
     */
    public function edit(Seo $seo)
    {
        // Ensure we only edit 'page' types via this route, or handle different views if needed
        if ($seo->type !== Seo::TYPE_PAGE) {
             abort(404, 'Record SEO entries are managed elsewhere.'); // Or redirect
        }
        return view('admin.seo.edit', compact('seo'));
    }

    /**
     * Update the specified SEO record in storage.
     */
    public function update(Request $request, Seo $seo)
    {
         if ($seo->type !== Seo::TYPE_PAGE) {
             abort(403, 'Cannot update this type of SEO record here.');
         }

        $validator = $this->validateSeo($request, $seo->id);
        if ($validator->fails()) {
            return redirect()->route('admin.seo.edit', $seo->id)->withErrors($validator)->withInput();
        }
        $validated = $validator->validated();

        // Force type just in case, though validation should handle it
        $validated['type'] = Seo::TYPE_PAGE;

        // Handle image uploads/removal
        $validated['og_image'] = $this->handleImageUpload($request, 'og_image', 'seo/og', $seo->og_image);
        $validated['twitter_image'] = $this->handleImageUpload($request, 'twitter_image', 'seo/twitter', $seo->twitter_image);


        $seo->update($validated);

        return redirect()->route('admin.seo.index')->with('success', 'SEO Page settings updated successfully.');
    }

    /**
     * Remove the specified SEO record from storage.
     */
    public function destroy(Seo $seo)
    {
        if ($seo->type !== Seo::TYPE_PAGE) {
             abort(403, 'Cannot delete this type of SEO record here.');
         }

        // Delete images before deleting the record
        $this->deleteImage($seo->og_image);
        $this->deleteImage($seo->twitter_image);

        $seo->delete();

        return redirect()->route('admin.seo.index')->with('success', 'SEO Page settings deleted successfully.');
    }

     // --- Helper: Validation Rules ---
    private function validateSeo(Request $request, ?int $seoId = null): \Illuminate\Validation\Validator
    {
        $rules = [
            'name' => 'required|string|max:255', // Admin readable name
            'key' => [                          // Unique key for the page
                'required',
                'string',
                'max:100',
                Rule::unique('seos', 'key')->ignore($seoId),
                'regex:/^[a-z0-9._-]+$/' // Allow lowercase, numbers, dot, underscore, hyphen
            ],
            // 'type' => ['required', Rule::in([Seo::TYPE_PAGE])], // We force 'page' in store/update
            'title' => 'required|string|max:70', // Max length recommendations
            'description' => 'required|string|max:160',
            'keywords' => 'nullable|string|max:255', // Comma-separated often
            'robots_meta' => 'nullable|string|max:100', // e.g., index, follow
            'canonical_url' => 'nullable|url|max:255',
            'og_title' => 'nullable|string|max:90',
            'og_description' => 'nullable|string|max:200',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:1024', // Max 1MB for OG
            'og_image_alt' => 'nullable|string|max:255',
            'og_locale' => 'nullable|string|max:10', // e.g., en_US
            'og_site_name' => 'nullable|string|max:100',
            'twitter_title' => 'nullable|string|max:70',
            'twitter_description' => 'nullable|string|max:200',
            'twitter_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:1024', // Max 1MB for Twitter card
            'twitter_alt' => 'nullable|string|max:255',
            'custom_meta_tags' => 'nullable|string|max:2000', // Allow more space for custom tags
        ];

         $messages = [
            'key.regex' => 'The key can only contain lowercase letters, numbers, dots, underscores, and hyphens.',
            'key.unique' => 'This page key is already in use.',
            'title.max' => 'Title is too long (max 70 characters recommended).',
            'description.max' => 'Description is too long (max 160 characters recommended).',
         ];

        return Validator::make($request->all(), $rules, $messages);
    }
}