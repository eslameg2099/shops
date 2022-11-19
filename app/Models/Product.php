<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use AhmedAliraqi\LaravelMediaUploader\Entities\Concerns\HasUploader;
use App\Models\Concerns\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use HasUploader;
  
    use HasMediaTrait;
   
    use Translatable;

    public $translatedAttributes = ['name','description'];

    protected $with = ['translations','media'];

    /**
     * The query parameter's filter of the model.
     *
     * @var string
     */

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shop_id',
        'category_id',
        'price',
        'offer_price',
        'has_discount',
      
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default')->onlyKeepLatest(5);
    }

    /**
     * Qualify price field before saving.
     *
     * @param $value
     */
    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = (float) $value;
    }

    /**
     * Qualify offer price field before saving.
     *
     * @param $value
     */
    public function setOfferPriceAttribute($value)
    {
        if (! $value) {
            return;
        }

        $this->attributes['offer_price'] = (float) $value;
    }

    /**
     * Retrieve the product's shop.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class)->withTrashed();
    }

     
    public function options()
    {
        return $this->hasMany(option::class,'product_id');
    }

    public function getPrice()
    {
        if ($this->has_discount) {
            return $this->offer_price;
        }
        return $this->price;
    }
}
