<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Parental\HasParent;
use App\Http\Resources\DelegateResource;
use App\Models\Relations\DelegateRelations;

class Delegate extends User
{
    use HasFactory;
    use HasParent;
    use DelegateRelations;

   
   


    public function getMorphClass()
    {
        return User::class;
    }

    /**
     * Get the default foreign key name for the model.
     *
     * @return string
     */
    public function getForeignKey()
    {
        return 'user_id';
    }

    /**
     * @return \App\Http\Resources\DelegateResource
     */
    public function getResource()
    {
        return new DelegateResource($this);
    }

    public function shopOrders()
    {
        return $this->hasMany(ShopOrder::class, 'delegate_id');
    }

  

}
