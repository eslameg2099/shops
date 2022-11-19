<?php

namespace App\Models;
use Parental\HasParent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Relations\ShopOwnerRelations;


class ShopOwner extends  User
{
    use HasFactory;
    use HasParent;
    use SoftDeletes;
    use ShopOwnerRelations;


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

  
    
}
