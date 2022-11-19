<?php

namespace App\Http\Resources;

use App\Support\Date;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'coupon'=> $this->coupon->code ?? null,
            'sub_total' => $this->sub_total,
            'discount' => $this->discount,
            'shipping_cost' => $this->shipping_cost,
            'total' => $this->total,
            'create_at' => new Date($this->created_at),
            'shipments_count' => $this->shopOrders->count(),
            'payment_method' => $this->payment_method,
            'readable_payment_method' => trans('orders.payments.'.$this->payment_method),
            'shipments' => ShopOrderResource::collection($this->shopOrders),
        ];
    }
}
