<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PolicyResource; // Import the resource
use App\Models\Policy;                 // Import the model
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    /**
     * Display a listing of the available policies (names and keys).
     * Typically public.
     */
    public function index()
    {
        // Fetch policies (add filtering for 'is_active' if you add that column)
        $policies = Policy::orderBy('name') // Or order by a specific display order if added
                           // ->where('is_active', true) // Uncomment if you add an active flag
                           ->get(['key', 'name', 'updated_at']); // Select only needed columns for listing

        // Use the resource collection
        return response()->json($policies);
    }


    /**
     * Display the specified policy content using its unique key.
     * Typically public.
     *
     * @param string $policyKey The unique key ('privacy-policy', 'return-policy', etc.)
     */
    public function show(string $policyKey)
    {
        $policy = Policy::where('key', $policyKey)
                        // ->where('is_active', true) // Uncomment if needed
                        ->first();

        if (!$policy) {
            return response()->json(['message' => 'Policy not found.'], 404);
        }

        // Use the resource to return the single item (will include content)
        return response()->json($policy);
    }

    // store, update, destroy methods would typically be in an Admin controller, not a public API
}