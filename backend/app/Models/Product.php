<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Can;

class Product extends Model
{
    use HasFactory;
public function laptop()
{
    return $this->hasOne(Laptop::class); 
    // 1 product có thể có 1 laptop
}
public function accessory()
{
    return $this->hasOne(accessory::class); 
    // 1 product có thể có 1 laptop
}
public function cart()
{
    return $this->hasOne(Cart::class); 
    // 1 product có thể có 1 laptop
}
}
