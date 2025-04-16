<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DiscountResource;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DiscountController extends Controller
{
    /**
     * Display a listing of currently active and valid discounts.
     * NOTE: This usually shouldn't list ALL discounts publicly.
     * Often, discount codes are applied directly in the cart/checkout.
     * This endpoint might be for showing *general* promotions, not unique codes.
     * Consider if this endpoint is truly needed publicly.
     */
    public function index(Request $request)
    {
        // Use the 'valid' scope defined in the Discount model if available and suitable
        // Or replicate the logic here:
        $now = Carbon::now();
        $query = Discount::where('status', Discount::STATUS_ACTIVE)
                        ->where(function($q) use ($now) {
                            $q->where('expiration_type', Discount::EXPIRATION_NONE)
                            ->orWhere(function($q2) use ($now) { // Valid period
                                $q2->where('expiration_type', Discount::EXPIRATION_PERIOD)
                                ->where(function($q3) use ($now){ $q3->whereNull('start_date')->orWhere('start_date', '<=', $now); })
                                ->where(function($q4) use ($now){ $q4->whereNull('end_date')->orWhere('end_date', '>=', $now); });
                            });
                            // Duration-based discounts usually aren't listed publicly like this
                        })
                        // Exclude discounts that might require specific codes?
                        // ->whereNull('code') // Example: only show general promotions? Adjust logic needed.
                        ->orderBy('created_at', 'desc');


        $discounts = $query->paginate($request->input('per_page', 10));

        return response()->json($discounts);
    }

    // Controller to *verify* a specific discount code during checkout is more common than listing all.
    // public function verifyCode(Request $request) { ... }

    // Other methods (show, store, update, destroy) would be here if needed (likely Admin only)
}