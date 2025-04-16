<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StorePageSectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // 'id' => $this->id, // Usually not needed by frontend for display
            'name' => $this->name,
            'key' => $this->key,
            'content' => $this->content, // The decoded JSON content (array/object)
            // 'updated_at' => $this->updated_at?->diffForHumans(), // Optional: How long ago updated
        ];
    }
}