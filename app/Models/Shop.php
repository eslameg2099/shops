<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use App\Models\Concerns\HasMediaTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\InteractsWithMedia;

use AhmedAliraqi\LaravelMediaUploader\Entities\Concerns\HasUploader;

class Shop extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use HasMediaTrait;
    use HasUploader;
    use SoftDeletes;


    protected $fillable = [
        'user_id',
        'name',
        'description',
        'address',
    ];

    /**
     * Define the media collections.
     *
     * @return void
     */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('logo')
            ->useFallbackUrl(url('/images/shop/logo.png'))
            ->singleFile();

        $this
            ->addMediaCollection('banner')
            ->useFallbackUrl(url('/images/shop/banner.png'))
            ->singleFile();
    }

    /**
     * The displayed image of the entity.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function image()
    {
        return $this->getFirstMediaUrl('logo');
    }

    /**
     * Retrieve the shop owner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(ShopOwner::class, 'user_id')->withTrashed();
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'shop_id');
    }

}
