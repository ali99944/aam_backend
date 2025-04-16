<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Models\City; // Adjust namespace if needed
use App\Models\DeliveryFee;
use Illuminate\Http\Request;

class DeliveryFeeApiController extends Controller
{
    /**
     * Summary of index
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Fetch active fees, ordered by name
        // Add filters if needed (e.g., by country_id if applicable)
        // $countryId = config('app.default_country_id'); // Example: Get default country ID
        $query = DeliveryFee::query();
                        // ->where('is_active', true) // Assuming City has an active flag
                        // ->where('country_id', $countryId)

        // Allow searching if needed for dynamic dropdowns
        // if ($request->filled('search')) {
        //     $query->where('name', 'like', '%' . $request->search . '%');
        // }

        $fees = $query->get();

        return response()->json($fees);
    }

    // Other methods (show, store, update, destroy) are likely not needed for public API
}