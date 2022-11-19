<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Support\Price;

class CartItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'price',
        'quantity',
        'option_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'price' => new Price($this->price),
            'color' => $this->option->color,
            'size' => $this->option->size,
            //'updated' => $this->wasUpdated(),
           // 'updated_message' => $this->getUpdateMessage(),
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'shop_name' => $this->product->shop->name,
                'image' => $this->product->getFirstMediaUrl(),
            ],
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function option()
    {
        return $this->belongsTo(option::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }


    
}
