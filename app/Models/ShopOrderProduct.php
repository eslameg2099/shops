<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopOrderProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_order_id',
        'product_id',
        'quantity',
        'price',
        'option_id',
    ];

    public function shopOrder()
    {
        return $this->belongsTo(ShopOrder::class);
    }

    /**
     * Retrieve the product instance.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }


    public function option()
    {
        return $this->belongsTo(option::class,'option_id');
    }
}
