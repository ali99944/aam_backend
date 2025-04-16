<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Translation; // Import Translation model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Import DB for transactions
use Illuminate\Support\Facades\Log; // Import Log
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LanguageController extends Controller
{
    // Reusable image handling logic (adapt from previous controllers or use a Trait)
    private function handleImageUpload(Request $request, string $fieldName, string $directory, ?string $oldPath = null): ?string
    {
        if ($request->hasFile($fieldName)) {
            if ($oldPath) Storage::disk('public')->delete($oldPath);
            return $request->file($fieldName)->store($directory, 'public');
        }
        return $oldPath; // Keep old if no new upload
    }
    private function deleteImage(?string $path): void { if ($path) Storage::disk('public')->delete($path); }

    public function index(Request $request)
    {
        $query = Language::query();
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where('name', 'like', $searchTerm)
                  ->orWhere('name_native', 'like', $searchTerm)
                  ->orWhere('locale', 'like', $searchTerm);
        }
        $languages = $query->orderBy('name')->paginate(15);
        return view('admin.languages.index', compact('languages'));
    }

    public function create()
    {
        $directions = Language::directions();
        return view('admin.languages.create', compact('directions'));
    }

    public function store(Request $request)
    {
        $validator = $this->validateLanguage($request);
        if ($validator->fails()) {
            return redirect()->route('admin.languages.create')->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();
        $validatedData['is_active'] = $request->has('is_active');
        $validatedData['flag_svg'] = $this->handleImageUpload($request, 'flag_svg', 'languages/flags');

        Language::create($validatedData);
        // Clear language cache if you implement one
        // Cache::forget('active_languages');
        return redirect()->route('admin.languages.index')->with('success', 'Language created successfully.');
    }

    public function edit(Language $language)
    {
        $directions = Language::directions();
        return view('admin.languages.edit', compact('language', 'directions'));
    }

    public function update(Request $request, Language $language)
    {
        $validator = $this->validateLanguage($request, $language->id);
        if ($validator->fails()) {
            return redirect()->route('admin.languages.edit', $language->id)->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();
        $validatedData['is_active'] = $request->has('is_active');
        $validatedData['flag_svg'] = $this->handleImageUpload($request, 'flag_svg', 'languages/flags', $language->flag_svg);

        $oldLocale = $language->locale;
        $newLocale = $validatedData['locale'];

        // Check if locale is changing and if the new locale already exists (other than current)
        if ($oldLocale !== $newLocale && Language::where('locale', $newLocale)->where('id', '!=', $language->id)->exists()) {
             return redirect()->route('admin.languages.edit', $language->id)
                         ->with('error', "Cannot update locale. Locale '{$newLocale}' is already in use.")
                         ->withInput();
        }

        DB::beginTransaction();
        try {
            // Update language details first
            $language->update($validatedData);

            // If locale changed, update all translations
            if ($oldLocale !== $newLocale) {
                Translation::where('locale', $oldLocale)->update(['locale' => $newLocale]);
            }

            DB::commit();
             // Clear language cache if you implement one
             // Cache::forget('active_languages');
            return redirect()->route('admin.languages.index')->with('success', 'Language updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating language ID {$language->id}: " . $e->getMessage());
             // Revert flag upload if needed (more complex, maybe just report error)
            return redirect()->route('admin.languages.edit', $language->id)
                         ->with('error', 'Failed to update language and translations.')
                         ->withInput();
        }
    }

    public function destroy(Language $language)
    {
        DB::beginTransaction();
        try {
            // 1. Delete all translations for this locale
            Translation::where('locale', $language->locale)->delete();

            // 2. Delete flag image
            $this->deleteImage($language->flag_svg);

            // 3. Delete the language record
            $language->delete();

            DB::commit();
            // Clear language cache if you implement one
            // Cache::forget('active_languages');
            return redirect()->route('admin.languages.index')->with('success', 'Language and its translations deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error deleting language ID {$language->id}: " . $e->getMessage());
            return redirect()->route('admin.languages.index')->with('error', 'Failed to delete language.');
        }
    }

    // --- Helper: Validation ---
    private function validateLanguage(Request $request, ?int $languageId = null): \Illuminate\Validation\Validator
    {
        $rules = [
            'name' => 'required|string|max:100',
            'name_native' => 'required|string|max:100',
            'locale' => [
                'required',
                'string',
                'max:10', // e.g., 'en', 'en-US'
                Rule::unique('languages', 'locale')->ignore($languageId),
                'regex:/^[a-zA-Z]{2}(?:[-_][a-zA-Z]{2})?$/' // Basic format check (e.g., en, en_US, en-US)
            ],
            'direction' => ['required', Rule::in(array_keys(Language::directions()))],
            'flag_svg' => 'nullable|file|mimes:svg|max:100', // Max 100KB SVG
            'is_active' => 'nullable|boolean',
        ];
        $messages = [
            'locale.regex' => 'Locale format is invalid. Use e.g., en, ar, en_US.',
            'flag_svg.mimes' => 'Flag must be an SVG file.',
        ];
        return Validator::make($request->all(), $rules, $messages);
    }
}