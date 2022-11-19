<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'sub_total',
        'shop_id',
        'status',
        'discount',
        'shipping_cost',
      
    ];


    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Retrieve the shop instance.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get the order's items.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(ShopOrderProduct::class,'shop_order_id');
    }

    public function delegate()
    {
        return $this->belongsTo(Delegate::class);
    }
    /**
     * Retrieve the delegate instance.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function scopeauth()
    {
        switch(auth()->user()->type) {
            case('shop_owner'):
 
            return ShopOrder::where('shop_id',auth()->user()->id);
                break;
 
            case('delegate'):
                 
            return ShopOrder::where('delegate_id',auth()->user()->id);
                break;
        }
    }
   
}
