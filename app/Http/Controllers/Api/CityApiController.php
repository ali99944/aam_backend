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
        $query = City::query()
                        ->where('is_active', true); // Assuming City has an active flag
                        // ->where('country_id', $countryId)

        // Allow searching if needed for dynamic dropdowns
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $cities = $query->orderBy('name')->get();

        return response()->json($cities);
    }

}