<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandApiController extends Controller
{

    /**
     * Summary of index
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Fetch active brands, ordered by name
        // Add filters if needed (e.g., by country_id if applicable)
        // $countryId = config('app.default_country_id'); // Example: Get default country ID
        $query = Brand::query();
                        // ->where('is_active', true) // Assuming City has an active flag
                        // ->where('country_id', $countryId)

        // Allow searching if needed for dynamic dropdowns
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $brands = $query->orderBy('name')->get();

        return response()->json($brands);
    }
}
