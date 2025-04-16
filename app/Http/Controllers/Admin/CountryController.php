<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Currency; // For dropdown
use App\Models\Timezone; // For dropdown
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CountryController extends Controller
{
     // Image handling helpers
    private function handleImageUpload(Request $request, string $fieldName, string $directory, ?string $oldPath = null): ?string {
         if ($request->hasFile($fieldName)) {
            if ($oldPath) Storage::disk('public')->delete($oldPath);
            // Use ISO2 code for filename to prevent clashes and make it predictable
            $filename = strtolower($request->input('iso2', 'xx')) . '.' . $request->file($fieldName)->getClientOriginalExtension();
            return $request->file($fieldName)->storeAs($directory, $filename, 'public');
         }
         return $oldPath;
    }
    private function deleteImage(?string $path): void { if ($path) Storage::disk('public')->delete($path); }

    public function index(Request $request) {
        $query = Country::with(['currency', 'timezone']); // Eager load relationships

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
             $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('iso2', 'like', $searchTerm)
                  ->orWhere('iso3', 'like', $searchTerm)
                  ->orWhere('capital', 'like', $searchTerm);
             });
        }
         if ($request->filled('is_active') && $request->is_active != 'all') {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        $countries = $query->orderBy('name')->paginate(25);
        return view('admin.countries.index', compact('countries'));
    }

    private function getFormData(): array
    {
         return [
            'currencies' => Currency::orderBy('name')->pluck('name', 'id'), // Assuming Currency model exists
            'timezones' => Timezone::orderBy('name')->pluck('name', 'id'), // Assuming Timezone model exists
        ];
    }

    public function create() {
        $formData = $this->getFormData();
        return view('admin.countries.create', $formData);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:countries,name',
            'iso2' => 'required|string|size:2|unique:countries,iso2',
            'iso3' => 'nullable|string|size:3|unique:countries,iso3',
            'phone_code' => 'nullable|string|max:20',
            'capital' => 'nullable|string|max:255',
            'currency_id' => 'nullable|exists:currencies,id',
            'timezone_id' => 'nullable|exists:timezones,id',
            'region' => 'nullable|string|max:255',
            'subregion' => 'nullable|string|max:255',
            'flag_image' => 'nullable|image|mimes:jpeg,png,gif,webp,svg|max:512', // Max 512KB for flags
            'is_active' => 'nullable|boolean',
        ],[
            'iso2.size' => 'ISO Alpha-2 code must be exactly 2 characters.',
            'iso3.size' => 'ISO Alpha-3 code must be exactly 3 characters.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.countries.create')->withErrors($validator)->withInput();
        }
        $validated = $validator->validated();
        $validated['is_active'] = $request->has('is_active');
        $validated['flag_image'] = $this->handleImageUpload($request, 'flag_image', 'flags');

        Country::create($validated);
        return redirect()->route('admin.countries.index')->with('success', 'Country created.');
    }

    public function edit(Country $country) {
        $formData = $this->getFormData();
        return view('admin.countries.edit', compact('country'), $formData);
    }

    public function update(Request $request, Country $country) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:countries,name,' . $country->id,
            'iso2' => 'required|string|size:2|unique:countries,iso2,' . $country->id,
            'iso3' => 'nullable|string|size:3|unique:countries,iso3,' . $country->id,
            'phone_code' => 'nullable|string|max:20',
            'capital' => 'nullable|string|max:255',
            'currency_id' => 'nullable|exists:currencies,id',
            'timezone_id' => 'nullable|exists:timezones,id',
            'region' => 'nullable|string|max:255',
            'subregion' => 'nullable|string|max:255',
            'flag_image' => 'nullable|image|mimes:jpeg,png,gif,webp,svg|max:512',
            'is_active' => 'nullable|boolean',
        ],[
            'iso2.size' => 'ISO Alpha-2 code must be exactly 2 characters.',
            'iso3.size' => 'ISO Alpha-3 code must be exactly 3 characters.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.countries.edit', $country->id)->withErrors($validator)->withInput();
        }
        $validated = $validator->validated();
        $validated['is_active'] = $request->has('is_active');
        $validated['flag_image'] = $this->handleImageUpload($request, 'flag_image', 'flags', $country->flag_image);

        // Keep old flag if no new one uploaded
        if (!$request->hasFile('flag_image') && array_key_exists('flag_image', $validated) && $validated['flag_image'] === null) {
             unset($validated['flag_image']);
        } elseif($validated['flag_image'] === null) {
            $validated['flag_image'] = $country->flag_image;
        }

        $country->update($validated);
        return redirect()->route('admin.countries.index')->with('success', 'Country updated.');
    }

    public function destroy(Country $country) {
        // Check if country has related cities/addresses/users first!
        if ($country->cities()->exists() /* || $country->addresses()->exists() */ ) {
            return back()->with('error', 'Cannot delete country with related cities/data.');
        }
        $this->deleteImage($country->flag_image);
        $country->delete();
        return redirect()->route('admin.countries.index')->with('success', 'Country deleted.');
    }
}