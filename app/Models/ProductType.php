<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{

    public function products()
    {
        return $this->hasMany(Product::class, 'type_id');
    }

    public function containers()
    {
        return $this->belongsToMany(Container::class, 'container_product_type');
    }

}
