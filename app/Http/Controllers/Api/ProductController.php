<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductDetailResource; // Use detail resource
use App\Http\Resources\ProductSummaryResource; // Use summary resource
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\Customer; // Your Customer model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Use Auth facade
use Illuminate\Support\Facades\DB; // For count queries
use Illuminate\Validation\Rule; // For validation

class ProductController extends Controller
{
    const DEFAULT_PER_PAGE = 15;
    const FEATURED_LIMIT = 5;

    /**
     * Display a listing of public and active products.
     * Supports filtering, sorting, and pagination.
     */
    public function index(Request $request)
    {
        $query = Product::with(['brand', 'subCategory', 'discount']) // Eager load basic relations
                        ->where('is_public', true)
                        ->where('status', Product::STATUS_ACTIVE);

        // --- Filtering ---
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(fn($q) => $q->where('name', 'like', $searchTerm)->orWhere('sku_code', 'like', $searchTerm));
        }
        if ($request->filled('sub_category_id')) {
            $query->where('sub_category_id', $request->sub_category_id);
        }
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // --- Sorting ---
        // $sortField = $request->input('sort_by', 'createdAt');
        // $sortDirection = $request->input('sort_dir', 'desc');
        // // Validate sort field against allowed fields
        // $allowedSortFields = ['createdAt', 'sell_price', 'name', 'overall_rating'];
        // if (in_array($sortField, $allowedSortFields)) {
        //      $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        // } else {
        //      $query->orderBy('createdAt', 'desc'); // Default sort
        // }

        if ($request->filled('min_price')) {
            $query->where('sell_price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('sell_price', '<=', $request->max_price);
        }
        // if ($request->filled('status')) {
        //     $query->where('stock_status', $request->status);
        // }
        // if ($request->filled('onlyDiscounted')) {
        //     // Need API support for this, e.g., /api/products?has_discount=true
        //     $query->whereHas('discount', function($q) {
        //         $q->whereNotNull('discount_id');
        //     });
        // }

        $products = $query->paginate($request->input('per_page', self::DEFAULT_PER_PAGE));

        return ProductSummaryResource::collection($products);
    }

    /**
     * Display the specified product details.
     */
    public function show(string $id) // Can be ID or potentially SKU/Slug
    {
        $product = Product::with([
                            'brand',
                            'subCategory.category', // Load parent category too?
                            'discount',
                            'images',
                            'specs' => fn($q) => $q->where('is_active', true), // Only active specs
                            'addons' => fn($q) => $q->where('is_active', true), // Only active addons
                           ])
                          ->where('is_public', true)
                          // ->where('status', Product::STATUS_ACTIVE) // Should inactive but public show? Decide this.
                          ->where(function($query) use ($id) {
                              $query->where('id', $id)
                                    ->orWhere('sku_code', $id); // Allow finding by SKU too
                                    // ->orWhere('slug', $id); // Allow finding by Slug if you add it
                          })
                          ->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found or not available.'], 404);
        }

        return response()->json($product);
    }

    /**
     * Get featured products grouped by criteria.
     */
    public function featured(Request $request)
    {
        $limit = self::FEATURED_LIMIT;
        $baseQuery = fn() => Product::with(['brand', 'discount']) // Minimal relations for summary
                                     ->where('is_public', true)
                                     ->where('status', Product::STATUS_ACTIVE);

        // 1. Newest Products
        $newest = $baseQuery()->latest()->take($limit)->get();

        // 2. Highest Purchased (Requires OrderItems relationship)
        $popular = $baseQuery()
                    ->withCount('orderItems') // Count related order items
                    ->orderByDesc('order_items_count')
                    ->take($limit)
                    ->get();

        // 3. Featured Flag
        $featured = $baseQuery()
                    ->where('is_featured', true)
                    ->orderByDesc('updated_at') // Show recently featured first?
                    ->take($limit)
                    ->get();

        // 4. Big Discounts (Simplified: products with any discount, ordered by creation)
        // For % or fixed amount sorting, you'd need more complex logic or derived columns.
        $discounted = $baseQuery()
                        ->whereNotNull('discount_id')
                        //->orderBy('discount_value_or_percentage', 'desc') // Need to calculate this
                        ->latest() // Simple sort for now
                        ->take($limit)
                        ->get();

        return response()->json([
            'newest' => ProductSummaryResource::collection($newest),
            'popular' => ProductSummaryResource::collection($popular),
            'featured' => ProductSummaryResource::collection($featured),
            'discounted' => ProductSummaryResource::collection($discounted),
        ]);
    }

    /**
     * Get products belonging to a specific active sub-category.
     */
    public function bySubCategory(Request $request, int $subCategoryId)
    {
         $subCategory = SubCategory::where('is_active', true)
                                   ->whereHas('category', fn($q) => $q->where('is_active', true))
                                   ->find($subCategoryId);

         if (!$subCategory) {
             return response()->json(['message' => 'Sub-category not found or is inactive.'], 404);
         }

         // Apply same filters/sorting as index() if needed
         $query = $subCategory->products()
                              ->with(['brand', 'discount'])
                              ->where('is_public', true)
                              ->where('status', Product::STATUS_ACTIVE);

         // --- Add Sorting (copy from index if desired) ---
         $query->orderBy('createdAt', 'desc'); // Example sort

         $products = $query->paginate($request->input('per_page', self::DEFAULT_PER_PAGE));

         return ProductSummaryResource::collection($products);
    }

    /**
     * Add or remove a product from the authenticated customer's favorites.
     */
    public function toggleFavorite(Request $request, Product $product) // Route model binding for product
    {
        /** @var Customer $customer */
        $customer = $request->user('customer'); // Get authenticated customer

        // Ensure product is valid to be favorited
        if (!$product->is_public /* || $product->status !== Product::STATUS_ACTIVE */) {
             return response()->json(['message' => 'Product cannot be favorited.'], 400);
        }

        // Toggle the relationship
        $result = $customer->favorites()->toggle($product->id);

        $isFavorited = count($result['attached']) > 0; // Check if it was attached (now favorited)

        return response()->json([
            'message' => $isFavorited ? 'Product added to favorites.' : 'Product removed from favorites.',
            'is_favorited' => $isFavorited,
        ]);
    }



    /**
     * Get the top 5 most sold products.
     */
    public function topSellers(Request $request)
    {
        $limit = 5;
        $products = Product::withCount('orderItems')
                            ->orderByDesc('order_items_count')
                            ->take($limit)
                            ->get();

        return response()->json($products);
    }

    /**
     * Get the top 5 newest products.
     */
    public function topNewest(Request $request)
    {
        $limit = 5;
        $products = Product::with(['brand', 'discount'])
                            ->where('is_public', true)
                            ->where('status', Product::STATUS_ACTIVE)
                            ->orderByDesc('created_at')
                            ->take($limit)
                            ->get();

        return ProductSummaryResource::collection($products);
    }

    /**
     * Get the top 5 products with the highest discount.
     */
    public function topDiscounts(Request $request)
    {
        $limit = 5;
        $products = Product::with(['brand', 'discount'])
                            ->whereHas('discount', function($q) {
                                $q->whereNotNull('discount_id');
                            })
                            ->orderByDesc('discount.value')
                            ->take($limit)
                            ->get();

        return ProductSummaryResource::collection($products);
    }
}