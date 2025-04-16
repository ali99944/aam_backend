<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProductImageResource; // Assuming you create this
use App\Http\Resources\ProductSpecResource; // Assuming you create this
use App\Http\Resources\ProductAddonResource; // Assuming you create this

class ProductDetailResource extends JsonResource
{
    /**
     * Add a flag indicating if the current authenticated user has favorited this product.
     */
    public function isFavoritedByCurrentUser(): bool
    {
        $customer = request()->user('sanctum_customer'); // Get authenticated customer
        return $customer ? $customer->hasFavorited($this->id) : false;
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'main_image_url' => $this->main_image_url,
            'cost_price' => (float) $this->cost_price, // Maybe hide cost price in public API?
            'sell_price' => (float) $this->sell_price,
            'stock' => $this->stock,
            'sku_code' => $this->sku_code,
            'overall_rating' => (float) $this->overall_rating,
            'status' => $this->status,
            'is_public' => $this->is_public,
            'is_featured' => $this->is_featured,
            'is_favorited' => $this->isFavoritedByCurrentUser(), // Check favorite status

            // Relationships
            'sub_category' => new SubCategoryResource($this->whenLoaded('subCategory')),
            'brand' => new BrandResource($this->whenLoaded('brand')),
            'discount' => new DiscountResource($this->whenLoaded('discount')), // Use DiscountResource if created
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
            'specs' => ProductSpecResource::collection($this->whenLoaded('specs')), // Maybe filter for active specs?
            'addons' => ProductAddonResource::collection($this->whenLoaded('addons')), // Maybe filter for active addons?

            'created_at' => $this->created_at?->diffForHumans(),
        ];
    }
}