<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OfferResource;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class OfferController extends Controller
{
    /**
     * Display a listing of currently active offers.
     */
    public function index(Request $request)
    {
        $now = Carbon::now();

        $offers = Offer::where('is_active', true)
                       // Check if within valid date range (if dates are set)
                       ->where(function($query) use ($now) {
                           $query->whereNull('start_date') // No start date means active now
                                 ->orWhere('start_date', '<=', $now);
                       })
                       ->where(function($query) use ($now) {
                           $query->whereNull('end_date') // No end date means never expires
                                 ->orWhere('end_date', '>=', $now);
                       })
                       ->orderBy('sort_order') // Use defined sort order
                       ->orderBy('created_at', 'desc')->get(); // Fallback sort

        return response()->json($offers);
    }

    // Other methods (show, store, update, destroy) would be here if needed (likely Admin only)
}