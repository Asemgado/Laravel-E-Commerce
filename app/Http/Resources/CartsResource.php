<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $items = $this->items ?? collect();

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'items' => $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product?->name,
                    'quantity' => (int) $item->quantity,
                    'price' => (float) $item->price,
                    'line_total' => round((float) $item->price * (int) $item->quantity, 2),
                ];
            })->values(),
            'totals' => [
                'items_count' => (int) $items->sum('quantity'),
                'subtotal' => round($items->sum(fn ($item) => (float) $item->price * (int) $item->quantity), 2),
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
