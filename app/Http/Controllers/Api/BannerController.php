<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BannerResource; // Create this resource
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    /**
     * Display a listing of active banners, ordered for the frontend.
     */
    public function index()
    {
        $banners = Banner::active() // Use the scope from the model
                          ->orderBy('sort_order')
                          ->orderBy('created_at', 'desc')
                          ->get();

        return response()->json($banners);
    }
}