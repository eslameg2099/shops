<?php

namespace App\Http\Resources;

use App\Support\Date;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'address'=> $this->address,
            'logo' => new MediaResource($this->getFirstMedia('logo')),
            'banner' => new MediaResource($this->getFirstMedia('banner')),
            'owner' => new ShopOwnerResource($this->whenLoaded('owner')),
            'rate' => (float) $this->rate,
            'address' => $this->address ?: 'Address ...',
            'created_at' => new Date($this->created_at),
        ];
    }
}
