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
        $customer = $request->user(); // Get authenticated customer

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
     * Get a list of the most recently added products.
     */
    public function justArrived(Request $request)
    {
        $products = Product::with(['brand', 'discount'])
                           ->where('is_public', true)
                           ->where('status', Product::STATUS_ACTIVE)
                           ->latest() // This orders by 'created_at' descending
                           ->get();

        return response()->json($products);
    }

    /**
     * Get a list of featured products.
     */
    public function featured(Request $request)
    {
        $products = Product::with(['brand', 'discount'])
                           ->where('is_public', true)
                           ->where('status', Product::STATUS_ACTIVE)
                           ->where('is_featured', true)
                           ->inRandomOrder() // Or latest(), or based on a 'featured_order' column
                           ->limit($request->input('limit', self::FEATURED_LIMIT))
                           ->get();

        return response()->json($products);
    }

    /**
     * Get a list of products with the highest percentage discounts.
     */
    public function topDiscounts()
    {
        $products = Product::with(['brand', 'discount'])
                            ->where('is_public', true)
                            ->where('status', Product::STATUS_ACTIVE)
                            // ->whereHas('discount', function($query) {
                            //     // Ensure discount is active and is a percentage type for ranking
                            //     $query->where('status', 'active')
                            //           ->where('type', 'percentage');
                            // })
                            // // Join to order by the discount value directly
                            // ->join('discounts', 'products.discount_id', '=', 'discounts.id')
                            // ->orderBy('discounts.value', 'desc') // Order by highest percentage
                            ->select('products.*') // Ensure we only select product columns after join
                            ->get();

        return response()->json($products);
    }


    /**
     * Get a list of recommended products for the authenticated user.
     * This requires a more complex recommendation logic.
     * Here's a basic implementation example.
     */
    public function recommended(Request $request)
    {
        /** @var Customer|null $customer */
        $customer = $request->user(); // Use your Sanctum guard for customers

        $query = Product::with(['brand', 'discount'])
                        ->where('is_public', true)
                        ->where('status', Product::STATUS_ACTIVE);

        if ($customer) {
            // --- Basic Recommendation Logic ---
            // 1. Find categories of products the user has bought or favorited recently.
            $interestedSubCategoryIds = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('orders.customer_id', $customer->id)
                ->where('orders.created_at', '>', now()->subMonths(6)) // In the last 6 months
                ->select('products.sub_category_id')
                ->distinct()
                ->pluck('sub_category_id');

            // 2. Prioritize products from those categories that the user hasn't bought yet.
            if ($interestedSubCategoryIds->isNotEmpty()) {
                $purchasedProductIds = DB::table('order_items')
                                         ->join('orders', 'order_items.order_id', '=', 'orders.id')
                                         ->where('orders.customer_id', $customer->id)
                                         ->pluck('order_items.product_id');

                $query->whereIn('sub_category_id', $interestedSubCategoryIds)
                      ->whereNotIn('id', $purchasedProductIds)
                      ->inRandomOrder(); // Show random products from the interested categories
            } else {
                // If no purchase history, fall back to showing highly-rated or featured products.
                $query->where('is_featured', true)
                      ->orWhere('overall_rating', '>=', 4) // Example: show popular products
                      ->inRandomOrder();
            }

        } else {
            // --- Guest User Recommendation ---
            // Fallback to showing featured or best-selling/highly-rated products.
             $query->where('is_featured', true)
                   ->orWhere('overall_rating', '>=', 4)
                   ->inRandomOrder();
        }

        $products = $query->get();

        // Ensure we don't return an empty list if logic fails, fallback again
        if ($products->isEmpty()) {
            $products = Product::with(['brand', 'discount'])
                                ->where('is_public', true)
                                ->where('status', Product::STATUS_ACTIVE)
                                ->where('is_featured', true)
                                ->inRandomOrder()
                                ->get();
        }

        // return ProductSummaryResource::collection($products);
        return response()->json($products);
    }
}