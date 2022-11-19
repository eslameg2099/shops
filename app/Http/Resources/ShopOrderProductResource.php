<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShopOrderProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product' => new MiniProductResource($this->product),
            'quantity' => (int) $this->quantity,
            'color' => $this->option->color,
            'size' => $this->option->size,
            'price' => $this->price,
            'total' => $this->total,
        ];
    }
}
