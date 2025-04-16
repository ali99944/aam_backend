<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LanguageResource;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * Display a listing of active languages.
     */
    public function index(Request $request)
    {
        // Usually return all active languages, no pagination needed unless you have hundreds
        $languages = Language::where('is_active', true)
                            ->orderBy('name') // Or a specific display order column
                            ->get();

        return response()->json($languages);
    }

    // Other methods (show, store, update, destroy) would be here if needed (likely Admin only)
}