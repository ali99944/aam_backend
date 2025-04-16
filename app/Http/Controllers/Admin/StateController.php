<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\State;
use App\Models\Country; // For dropdowns
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StateController extends Controller
{
    public function index(Request $request) {
        $query = State::with('country'); // Eager load country

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('state_code', 'like', $searchTerm)
                  ->orWhereHas('country', fn($cq) => $cq->where('name', 'like', $searchTerm));
            });
        }
        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }
         if ($request->filled('is_active') && $request->is_active != 'all') {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        // Join for sorting by country name if needed, or sort by state name
        $states = $query->select('states.*') // Ensure we only select state columns if joining
                        ->join('countries', 'states.country_id', '=', 'countries.id') // Example join for sorting
                        ->orderBy('countries.name') // Sort by country first
                        ->orderBy('states.name') // Then by state name
                        ->paginate(25);


        $countries = Country::where('is_active', true)->orderBy('name')->pluck('name', 'id');

        return view('admin.states.index', compact('states', 'countries'));
    }

     private function getFormData(): array
    {
         return [
            'countries' => Country::where('is_active', true)->orderBy('name')->pluck('name', 'id'),
        ];
    }

    public function create() {
        $formData = $this->getFormData();
        return view('admin.states.create', $formData);
    }

    public function store(Request $request) {
         $validator = Validator::make($request->all(), [
            'country_id' => 'required|exists:countries,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('states')->where(function ($query) use ($request) {
                    return $query->where('country_id', $request->country_id); // Unique name within the country
                }),
            ],
            'state_code' => 'nullable|string|max:10',
            'is_active' => 'nullable|boolean',
        ],[
            'name.unique' => 'This state name already exists for the selected country.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.states.create')->withErrors($validator)->withInput();
        }
        $validated = $validator->validated();
        $validated['is_active'] = $request->has('is_active');

        State::create($validated);
        return redirect()->route('admin.states.index')->with('success', 'State created.');
    }

    public function edit(State $state) {
        $formData = $this->getFormData();
        // Ensure the currently selected country is in the list, even if inactive
        if (!$formData['countries']->has($state->country_id)) {
            $currentCountry = Country::find($state->country_id);
             if($currentCountry){
                 $formData['countries']->put($currentCountry->id, $currentCountry->name . ' (Inactive)');
             }
        }
        return view('admin.states.edit', compact('state'), $formData);
    }

    public function update(Request $request, State $state) {
        $validator = Validator::make($request->all(), [
            'country_id' => 'required|exists:countries,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('states')->where(function ($query) use ($request) {
                    return $query->where('country_id', $request->country_id);
                })->ignore($state->id), // Ignore current state ID
            ],
            'state_code' => 'nullable|string|max:10',
            'is_active' => 'nullable|boolean',
        ],[
             'name.unique' => 'This state name already exists for the selected country.',
        ]);

         if ($validator->fails()) {
            return redirect()->route('admin.states.edit', $state->id)->withErrors($validator)->withInput();
        }
        $validated = $validator->validated();
        $validated['is_active'] = $request->has('is_active');

        $state->update($validated);
        return redirect()->route('admin.states.index')->with('success', 'State updated.');
    }

    public function destroy(State $state) {
        // Check if state has related cities
        if ($state->cities()->exists()) {
            return back()->with('error', 'Cannot delete state with existing cities linked to it.');
        }
        $state->delete();
        return redirect()->route('admin.states.index')->with('success', 'State deleted.');
    }
}