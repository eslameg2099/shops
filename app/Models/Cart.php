<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Support\Price;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier',
        'user_id',
        'payment_method',
        'notes',
        'sub_total',
        'shipping_cost',
        'total',
    ];

    /**
     * Get the cart user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the cart address.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
  

    /**
     * Get all the cart items.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'identifier' => $this->identifier,
            'payment_method' => (int) $this->payment_method,
            'readable_payment_method' => ! is_null($this->payment_method)
                ? trans('orders.payments.'.$this->payment_method)
                : null,
            'notes' => $this->notes,
            'sub_total' => new Price($this->sub_total),
            'discount' => new Price($this->discount),
            'total' => new Price($this->sub_total ),
            'items' => $this->items,
        ];
    }
}
