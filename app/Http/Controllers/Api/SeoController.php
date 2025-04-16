<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SeoResource; // Import the resource
use App\Models\Seo; // Import the model
use Illuminate\Http\Request;

class SeoController extends Controller
{
    /**
     * Retrieve SEO metadata for a specific page or record type using its key.
     *
     * @param string $key The unique key identifying the SEO record.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSeoByKey(string $key)
    {
        // Find the SEO record by its unique key
        $seoData = Seo::where('key', $key)->first();

        // Handle case where no specific SEO data is found for the key
        if (!$seoData) {
            // Option 1: Return 404 Not Found
            // return response()->json(['message' => 'SEO data not found for this key.'], 404);

            // Option 2: Return default SEO data (fetch a 'default' record or generate)
            $defaultSeo = Seo::where('key', 'default')->first(); // Assuming you create a 'default' entry
            if ($defaultSeo) {
                return response()->json($defaultSeo);
            } else {
                // Fallback if even default isn't found
                 return response()->json(['message' => 'SEO data not found.'], 404);
                 // Or return a minimal default JSON structure
                 // return response()->json([ 'title' => config('app.name'), 'description' => 'Welcome to ' . config('app.name') ]);
            }
        }

        // Return the formatted SEO data using the resource
        return response()->json($seoData);
    }

/**
 * Display a listing of all SEO records.
 *
 * @return \Illuminate\Http\JsonResponse
 */
public function index()
{
    // Retrieve all SEO records
    $seoRecords = Seo::all();

    // Return the collection of SEO records using the resource collection
    return response()->json($seoRecords);
}


    // No index, store, update, destroy methods needed for this public API endpoint
}