<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'product_id',
        'brand_name',
        'detail',
        'image',
        'price'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}