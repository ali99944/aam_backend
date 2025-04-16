<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StorePage;
use App\Models\StorePageSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
// Removed use MessageBag;

class StorePageSectionController extends Controller
{
    // --- Image/File helpers are NOT needed anymore ---

    // Show create form
    public function create(StorePage $storePage) {
        return view('admin.store_page_sections.create', compact('storePage'));
    }

    // Store a new section
    public function store(Request $request, StorePage $storePage) {
        // Combined simplified validation
         $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'key' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9_-]+$/', 'unique:store_page_sections,key'],
            'content' => ['required', 'json'], // Just check if it's valid JSON string
         ], [
            'key.regex' => 'The key can only contain lowercase letters, numbers, underscores, and hyphens.',
            'key.unique' => 'This section key is already taken.',
            'content.required' => 'Content JSON cannot be empty.',
            'content.json' => 'The provided content is not valid JSON.',
         ]);

         if ($validator->fails()) {
             return redirect()->route('admin.store-pages.sections.create', $storePage->id)
                              ->withErrors($validator)->withInput();
         }
         $validated = $validator->validated();

         // Decode validated content (already checked as valid JSON)
         // The model's cast will handle encoding on save
         $contentArray = json_decode($validated['content'], true);

         DB::beginTransaction();
         try {
            $section = $storePage->sections()->create([
                'name' => $validated['name'],
                'key' => $validated['key'],
                'content' => $contentArray, // Store the decoded array
            ]);
            DB::commit();
            return redirect()->route('admin.store-pages.show', $storePage->id)
                             ->with('success', 'Section "' . $section->name . '" created successfully.');
         } catch (\Exception $e) {
             DB::rollBack();
             Log::error("Error creating section for page {$storePage->id}: " . $e->getMessage());
             return redirect()->route('admin.store-pages.sections.create', $storePage->id)
                              ->with('error', 'Failed to create section: ' . $e->getMessage())
                              ->withInput();
         }
    }

    // Show edit form
    public function edit(StorePage $storePage, StorePageSection $section) {
         if ($section->store_page_id !== $storePage->id) abort(404);
        return view('admin.store_page_sections.edit', compact('storePage', 'section'));
    }

    // Update a section
    public function update(Request $request, StorePage $storePage, StorePageSection $section) {
         if ($section->store_page_id !== $storePage->id) abort(404);

         // Combined simplified validation
         $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'key' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9_-]+$/', Rule::unique('store_page_sections')->ignore($section->id)],
            'content' => ['required', 'json'],
         ], [
            'key.regex' => 'The key can only contain lowercase letters, numbers, underscores, and hyphens.',
            'key.unique' => 'This section key is already taken.',
            'content.required' => 'Content JSON cannot be empty.',
            'content.json' => 'The provided content is not valid JSON.',
         ]);

         if ($validator->fails()) {
            return redirect()->route('admin.store-pages.sections.edit', [$storePage->id, $section->id])
                             ->withErrors($validator)->withInput();
        }
        $validated = $validator->validated();

         // Decode validated content
         $contentArray = json_decode($validated['content'], true);

         DB::beginTransaction();
         try {
             $section->update([
                 'name' => $validated['name'],
                 'key' => $validated['key'],
                 'content' => $contentArray,
             ]);
             DB::commit();
             return redirect()->route('admin.store-pages.show', $storePage->id)
                              ->with('success', 'Section "' . $section->name . '" updated successfully.');
         } catch (\Exception $e) {
             DB::rollBack();
             Log::error("Error updating section {$section->id}: " . $e->getMessage());
             return redirect()->route('admin.store-pages.sections.edit', [$storePage->id, $section->id])
                              ->with('error', 'Failed to update section: ' . $e->getMessage())
                              ->withInput();
         }
    }

    // Delete a section
    public function destroy(StorePage $storePage, StorePageSection $section) {
         if ($section->store_page_id !== $storePage->id) abort(404);
         try {
             // No file deletion needed
             $section->delete();
             return redirect()->route('admin.store-pages.show', $storePage->id)
                              ->with('success', 'Section "' . $section->name . '" deleted successfully.');
         } catch (\Exception $e) {
             Log::error("Error deleting section {$section->id}: " . $e->getMessage());
             return redirect()->route('admin.store-pages.show', $storePage->id)
                              ->with('error', 'Failed to delete section.');
         }
    }

    // --- Remove validateSectionBase and validateContentStructure helpers ---

}