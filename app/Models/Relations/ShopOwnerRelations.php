<?php

namespace App\Models\Relations;

use App\Models\Shop;
use App\Models\Product;

trait ShopOwnerRelations
{
    /**
     * Get the user's shops.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shops()
    {
        return $this->hasMany(Shop::class, 'user_id');
    }

    /**
     * Get the user's shop.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function shop()
    {
        return $this->hasOne(Shop::class, 'user_id')->withTrashed();
    }

    /**
     * Get the user's shops.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function products()
    {
        return $this->hasManyThrough(Product::class, Shop::class);
    }
}
