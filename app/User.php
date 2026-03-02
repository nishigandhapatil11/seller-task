<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'country',
        'state',
        'skills',
        'password',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'skills' => 'array',   // important for JSON skills
    ];

    // Seller Products Relationship
 public function products()
{
    return $this->hasMany(Product::class, 'seller_id');
}
    
}