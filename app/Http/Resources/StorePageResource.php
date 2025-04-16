<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StorePageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // 'id' => $this->id, // Usually not needed by frontend
            'name' => $this->name,
            'key' => $this->key,
            // Conditionally load sections based on request or relationship presence
            'sections' => StorePageSectionResource::collection($this->whenLoaded('sections')),
            // 'created_at' => $this->created_at?->toISOString(), // Optional
        ];
    }
}