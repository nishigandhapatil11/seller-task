<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'seller_id',
        'product_name',
        'product_description'
    ];

    public function brands()
    {
        return $this->hasMany(Brand::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}