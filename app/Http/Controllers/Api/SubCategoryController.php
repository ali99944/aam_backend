<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubCategoryResource;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of active sub-categories.
     */
    public function index(Request $request)
    {
        $query = SubCategory::with('category')
                            ->where('is_active', true) // Only active sub-cats
                            ->whereHas('category', fn($q) => $q->where('is_active', true)); // Only from active parent cats

        if ($request->filled('category_id')) {
             $query->where('category_id', $request->category_id);
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $subCategories = $query->orderBy('name')->paginate($request->input('per_page', 100));

        return response()->json($subCategories);
    }


    /**
     * Get subcategories for a given category ID.
     */
    public function byCategoryId(int $categoryId)
    {
        $subCategories = SubCategory::with('category')
                                    ->where('category_id', $categoryId)
                                    ->where('is_active', true) // Only active sub-cats
                                    ->orderBy('name')
                                    ->get();

        return response()->json($subCategories);
    }
}