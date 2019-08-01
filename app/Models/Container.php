<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_container');
    }

    public function product_types()
    {
        return $this->belongsToMany(ProductType::class, 'container_product_type');
    }

}
