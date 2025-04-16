<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryFee;
use App\Models\City; // Adjust namespace if needed
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DeliveryFeeController extends Controller
{
    public function index(Request $request) {
        $query = DeliveryFee::with('city'); // Eager load city

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            // Search by City name using the relationship
            $query->whereHas('city', function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm); // Assumes 'name' column on City model
            });
        }
         if ($request->filled('is_active') && $request->is_active != 'all') {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        $deliveryFees = $query->join('cities', 'delivery_fees.city_id', '=', 'cities.id') // Join for sorting by city name
                              ->orderBy('cities.name') // Sort by City name
                              ->select('delivery_fees.*') // Select only delivery_fees columns after join
                              ->paginate(20);

        return view('admin.delivery_fees.index', compact('deliveryFees'));
    }

    public function create() {
        // Get cities that DON'T already have a fee assigned
        $availableCities = City::whereDoesntHave('deliveryFee')
                                // ->where('country_id', YOUR_COUNTRY_ID) // Optional: Filter by country if needed
                                ->orderBy('name')
                                ->pluck('name', 'id'); // Assumes 'name' column

        if($availableCities->isEmpty()){
             return redirect()->route('admin.delivery-fees.index')->with('warning', 'All available cities already have delivery fees assigned.');
        }

        return view('admin.delivery_fees.create', compact('availableCities'));
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'city_id' => 'required|exists:cities,id|unique:delivery_fees,city_id', // Ensure city exists and fee is unique for it
            'amount' => 'required|numeric|min:0',
            'estimated_delivery_time' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string|max:1000',
        ],[
            'city_id.unique' => 'A delivery fee has already been set for the selected city.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.delivery-fees.create')->withErrors($validator)->withInput();
        }
        $validated = $validator->validated();
        $validated['is_active'] = $request->has('is_active');

        DeliveryFee::create($validated);
        return redirect()->route('admin.delivery-fees.index')->with('success', 'Delivery Fee created.');
    }

    public function edit(DeliveryFee $deliveryFee) {
         $deliveryFee->load('city'); // Load city data for display
        // No need to pass cities list as we don't change the city here
        return view('admin.delivery_fees.edit', compact('deliveryFee'));
    }

    public function update(Request $request, DeliveryFee $deliveryFee) {
        $validator = Validator::make($request->all(), [
            // city_id is not updatable here, handled by create/delete
            'amount' => 'required|numeric|min:0',
            'estimated_delivery_time' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

         if ($validator->fails()) {
            return redirect()->route('admin.delivery-fees.edit', $deliveryFee->id)->withErrors($validator)->withInput();
        }
        $validated = $validator->validated();
        $validated['is_active'] = $request->has('is_active');

        $deliveryFee->update($validated);
        return redirect()->route('admin.delivery-fees.index')->with('success', 'Delivery Fee updated.');
    }

    public function destroy(DeliveryFee $deliveryFee) {
        $deliveryFee->delete();
        return redirect()->route('admin.delivery-fees.index')->with('success', 'Delivery Fee deleted.');
    }
}