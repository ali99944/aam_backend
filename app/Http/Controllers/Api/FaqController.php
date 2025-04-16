<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($key)
    {
        $category = FaqCategory::where('key', $key)->with('faqs')->firstOrFail();
        $faqs = $category->faqs;

        return response()->json($faqs);
    }
}
