<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accessory extends Model
{
    use HasFactory;
    protected $fillable = ['productID', 'des'];
        public function product()
{
    return $this->belongsTo(Product::class, 'productID', 'id');
}
}
