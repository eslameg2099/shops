<?php

namespace App\Http\Resources;

use App\Support\Date;
use App\Support\Price;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed offer_price
 * @property mixed price
 */
class OptionResource extends JsonResource
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
            'color' => $this->color,
            'size' => $this->size,
            'hex' => $this->hex,
            'quantity' => $this->quantity,

          
        ];
    }

    /**
     * Get the discount percentage.
     *
     * @return float|int|void
     */
   
}
