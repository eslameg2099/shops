<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sub_total',
        'discount',
        'notes',
        'shipping_cost',
        'payment_method',
        
    ];

    public function user()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Retrieve the address instance.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
   
    /**
     * Get the shop orders that associated the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shopOrders()
    {
        return $this->hasMany(ShopOrder::class);
    }
}
