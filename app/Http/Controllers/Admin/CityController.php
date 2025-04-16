<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\State;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CityController extends Controller
{
    public function index(Request $request) {
        $query = City::with(['state', 'country']); // Eager load relationships

        // --- Filtering ---
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
             $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhereHas('state', fn($sq) => $sq->where('name', 'like', $searchTerm))
                  ->orWhereHas('country', fn($cq) => $cq->where('name', 'like', $searchTerm));
             });
        }
        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }
        if ($request->filled('state_id')) {
            $query->where('state_id', $request->state_id);
        }
         if ($request->filled('is_active') && $request->is_active != 'all') {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        // Sorting - Join necessary tables if sorting by related names
        $cities = $query->select('cities.*')
                       ->join('states', 'cities.state_id', '=', 'states.id')
                       ->join('countries', 'cities.country_id', '=', 'countries.id')
                       ->orderBy('countries.name', 'asc')
                       ->orderBy('states.name', 'asc')
                       ->orderBy('cities.name', 'asc')
                       ->paginate(25);


        $countries = Country::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        // Get states relevant to the selected country filter, or all active states
        $statesQuery = State::where('is_active', true);
        if ($request->filled('country_id')) {
            $statesQuery->where('country_id', $request->country_id);
        }
        $states = $statesQuery->orderBy('name')->pluck('name', 'id');


        return view('admin.cities.index', compact('cities', 'countries', 'states'));
    }

    private function getFormData(): array
    {
         // Get active countries and states, grouped by country for potentially better dropdowns
         $countries = Country::where('is_active', true)->orderBy('name')->pluck('name', 'id');
         // Eager load states with countries for efficient grouping
         $statesGrouped = State::with('country')
                            ->where('is_active', true)
                            ->whereHas('country', fn($q) => $q->where('is_active', true))
                            ->orderBy('name')
                            ->get()
                            ->groupBy('country.name'); // Group by country name

        return [
            'countries' => $countries,
            'statesGrouped' => $statesGrouped, // Pass the grouped collection
            // Or just a flat list:
            // 'states' => State::where('is_active', true)->orderBy('name')->pluck('name', 'id'),
        ];
    }

     public function create() {
        $formData = $this->getFormData();
        // Pass an empty City object to avoid errors in the form partial for optional fields
        $city = new City();
        return view('admin.cities.create', $formData, compact('city'));
    }

    public function store(Request $request) {
         $validator = Validator::make($request->all(), [
            // Country is implicitly set via State, but validate if needed for direct link
            // 'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('cities')->where(function ($query) use ($request) {
                    // City name must be unique within the selected state
                    return $query->where('state_id', $request->state_id);
                }),
            ],
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'nullable|boolean',
        ],[
            'name.unique' => 'This city name already exists in the selected state.',
            'latitude.between' => 'Latitude must be between -90 and 90.',
            'longitude.between' => 'Longitude must be between -180 and 180.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.cities.create')->withErrors($validator)->withInput();
        }
        $validated = $validator->validated();
        $validated['is_active'] = $request->has('is_active');

        // Automatically set country_id based on the selected state's country
        $state = State::find($validated['state_id']);
        if ($state) {
            $validated['country_id'] = $state->country_id;
        } else {
             // This case should technically be caught by 'exists:states,id' validation
             return redirect()->route('admin.cities.create')->with('error', 'Selected state not found.')->withInput();
        }

        City::create($validated);
        return redirect()->route('admin.cities.index')->with('success', 'City created.');
    }

     public function edit(City $city) {
        $formData = $this->getFormData();
        $city->load(['state', 'country']); // Ensure relationships are loaded

        // Ensure the city's current state/country are in the lists
         if (!$formData['countries']->has($city->country_id)) {
             $currentCountry = Country::find($city->country_id);
             if($currentCountry){ $formData['countries']->put($currentCountry->id, $currentCountry->name . ' (Inactive)'); }
         }
          // Add state if needed, though grouped list should handle this if fetched correctly
        // if ($city->state && !$formData['statesGrouped']->flatten()->pluck('id')->contains($city->state_id)) { ... }


        return view('admin.cities.edit', $formData, compact('city'));
    }

    public function update(Request $request, City $city) {
         $validator = Validator::make($request->all(), [
            'state_id' => 'required|exists:states,id',
            'name' => [
                'required',
                'string',
                'max:255',
                 Rule::unique('cities')->where(function ($query) use ($request) {
                    return $query->where('state_id', $request->state_id);
                })->ignore($city->id), // Ignore current city ID
            ],
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'nullable|boolean',
        ],[
             'name.unique' => 'This city name already exists in the selected state.',
             'latitude.between' => 'Latitude must be between -90 and 90.',
            'longitude.between' => 'Longitude must be between -180 and 180.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.cities.edit', $city->id)->withErrors($validator)->withInput();
        }
        $validated = $validator->validated();
        $validated['is_active'] = $request->has('is_active');

        // Update country_id if state changed
        $state = State::find($validated['state_id']);
        if ($state && $state->country_id != $city->country_id) {
            $validated['country_id'] = $state->country_id;
        } elseif (!$state) {
             return redirect()->route('admin.cities.edit', $city->id)->with('error', 'Selected state not found.')->withInput();
        }

        $city->update($validated);
        return redirect()->route('admin.cities.index')->with('success', 'City updated.');
    }

    public function destroy(City $city) {
        // Check for related data: Delivery Fees, Addresses, Orders etc.
        if ($city->deliveryFee()->exists() /* || $city->addresses()->exists() */) {
             return back()->with('error', 'Cannot delete city with related data (e.g., delivery fees, addresses).');
        }
        $city->delete();
        return redirect()->route('admin.cities.index')->with('success', 'City deleted.');
    }
}