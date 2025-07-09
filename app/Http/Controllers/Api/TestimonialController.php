<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TestimonialResource; // Create this resource
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    /**
     * Display a listing of active testimonials.
     */
    public function index()
    {
        $testimonials = Testimonial::active()
                                   ->orderBy('sort_order')
                                   ->orderBy('created_at', 'desc')
                                   ->get();

        return response()->json($testimonials);
    }
}