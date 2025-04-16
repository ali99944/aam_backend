<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Models\City; // Adjust namespace if needed
use Illuminate\Http\Request;

class CityApiController extends Controller
{
    /**
     * Display a listing of active cities, typically for dropdowns.
     * No pagination applied.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        // Fetch active cities, ordered by name
        // Add filters if needed (e.g., by country_id if applicable)
        // $countryId = config('app.default_country_id'); // Example: Get default country ID
        $query = City::query();
                        // ->where('is_active', true) // Assuming City has an active flag
                        // ->where('country_id', $countryId)

        // Allow searching if needed for dynamic dropdowns
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $cities = $query->orderBy('name')->get();

        return response()->json($cities);
    }

    // Other methods (show, store, update, destroy) are likely not needed for public API
}