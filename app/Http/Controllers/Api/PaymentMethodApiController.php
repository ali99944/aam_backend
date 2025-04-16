<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentMethodResource;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodApiController extends Controller
{
    /**
     * Display a listing of enabled payment methods for checkout.
     * No pagination applied.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        // Fetch only enabled methods, ordered by display order
        $methods = PaymentMethod::where('is_enabled', true)
                                ->orderBy('display_order')
                                ->orderBy('name')
                                ->get();

        return response()->json($methods);
    }

     // Other methods (show, store, update, destroy) are usually admin-only
}