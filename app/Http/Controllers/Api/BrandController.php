<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of active brands.
     */
    public function index(Request $request)
    {
        // Assuming brands don't have an 'is_active' flag based on schema
        // If you add one, filter here: ->where('is_active', true)
        $brands = Brand::orderBy('name')
                       ->paginate($request->input('per_page', 50)); // Paginate results

        return response()->json($brands);
    }

    // Other methods (show, store, update, destroy) would be here if needed
}