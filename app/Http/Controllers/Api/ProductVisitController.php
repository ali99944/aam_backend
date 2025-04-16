<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductVisitController extends Controller
{

    public function all(Request $request)
    {
        // $customer = auth()->guard('sanctum_customer')->user();
        // return $customer->visitedProducts()->orderByDesc('pivot_created_at')->get();
    }

    public function checkIfVisited(Request $request, $product)
    {
        // $customer = auth()->guard('sanctum_customer')->user();
        // return $customer->visitedProducts()->where('product_id', $product)->exists();
    }
}
