<?php

namespace App\Http\Resources;

use App\Support\Date;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\ShopOrder */
class ShopOrderResource extends JsonResource
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
            'status' => $this->status,
            'shop' => new ShopResource($this->shop),
            'delegate' => new DelegateResource($this->delegate),

            'sub_total' => $this->sub_total,
            'discount' =>$this->discount,
            'shipping_cost' => $this->shipping_cost,
            'total' => $this->total,
          
            'create_at' => new Date($this->created_at),
            'items_count' => (int) $this->items->count(),
          
            'items' => ShopOrderProductResource::collection($this->whenLoaded('items')),
        ];
    }
}
