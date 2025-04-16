<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sell_price' => (float) $this->sell_price,
            'main_image_url' => $this->main_image_url,
            'stock' => $this->stock,
            'status' => $this->status,
            'is_featured' => $this->is_featured, // Added
            // Add overall_rating if needed
             // 'overall_rating' => (float) $this->overall_rating,
        ];
    }
}